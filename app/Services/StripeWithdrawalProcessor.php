<?php

namespace App\Services;

use App\Mail\WithdrawalIssue;
use App\Mail\WithdrawalProcessing;
use App\Models\UserFunds;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;

class StripeWithdrawalProcessor
{
    public function process(Withdrawal $withdrawal): Withdrawal
    {
        if ($withdrawal->status !== Withdrawal::STATUS_PENDING) {
            throw new \Exception('This withdrawal has already been processed.');
        }

        $withdrawal->loadMissing('user');
        if (!$withdrawal->user) {
            throw new \Exception('Withdrawal user not found.');
        }

        $amount = (float) $withdrawal->amount;
        if ($amount <= 0) {
            throw new \Exception('Withdrawal amount must be greater than zero.');
        }

        $currency = strtolower($withdrawal->currency ?? 'usd');
        $amountInCents = (int) round($amount * 100);

        try {
            // NOTE: this creates a platform payout (not a per-destination transfer).
            // It matches current project mechanics, and lets us track payout status via webhook.
            $stripePayout = Cashier::stripe()->payouts->create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'metadata' => [
                    'withdrawal_id' => $withdrawal->id,
                    'user_id' => $withdrawal->user_id,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe withdrawal payout creation failed', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);

            // Mark failed + refund
            $this->markFailedAndRefund($withdrawal, 'Failed to create payout in Stripe: ' . $e->getMessage());
            throw new \Exception('Failed to create payout in Stripe: ' . $e->getMessage());
        }

        $destinationLabel = $this->describeDestination($withdrawal);

        DB::transaction(function () use ($withdrawal, $stripePayout) {
            $withdrawal->forceFill([
                'status' => Withdrawal::STATUS_PROCESSING,
                'stripe_payout_id' => $stripePayout->id,
                'failure_message' => null,
                'processed_at' => now(),
            ])->save();
        });

        // VII.email.6 (TЗ) — once per withdrawal
        $key = 'email_withdrawal_processing_' . $withdrawal->id;
        if (!$withdrawal->user->notifications()->where('group', $key)->exists()) {
            try {
                Mail::to($withdrawal->user->email)->send(new WithdrawalProcessing(
                    user: $withdrawal->user,
                    amountLabel: '$' . number_format((float) $withdrawal->amount, 2),
                    destinationLabel: $destinationLabel,
                ));
                $withdrawal->user->notifications()->create([
                    'type' => 'info',
                    'group' => $key,
                    'message' => json_encode(['sent_at' => now()->toDateTimeString()]),
                    'show' => 0,
                    'closable' => 0,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to send withdrawal processing email', [
                    'withdrawal_id' => $withdrawal->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $withdrawal->refresh();
    }

    public function updateStatusFromStripe(string $stripePayoutId, string $status, ?string $failureMessage = null): void
    {
        $withdrawal = Withdrawal::where('stripe_payout_id', $stripePayoutId)->first();
        if (!$withdrawal) {
            return;
        }

        $newStatus = match ($status) {
            'paid' => Withdrawal::STATUS_COMPLETED,
            'failed', 'canceled' => Withdrawal::STATUS_FAILED,
            'pending', 'in_transit' => Withdrawal::STATUS_PROCESSING,
            default => $withdrawal->status,
        };

        if ($newStatus === $withdrawal->status && empty($failureMessage)) {
            return;
        }

        $withdrawal->update([
            'status' => $newStatus,
            'failure_message' => $failureMessage ?: $withdrawal->failure_message,
            'processed_at' => now(),
        ]);

        if ($newStatus === Withdrawal::STATUS_FAILED) {
            $this->markFailedAndRefund($withdrawal, $failureMessage ?: ($withdrawal->failure_message ?? 'Withdrawal failed'));
            $this->notifyFailedOnce($withdrawal, $failureMessage ?: ($withdrawal->failure_message ?? 'Withdrawal failed'));
        }
    }

    public function markRejectedAndRefund(Withdrawal $withdrawal, string $reason): void
    {
        if (!in_array($withdrawal->status, [Withdrawal::STATUS_PENDING, Withdrawal::STATUS_PROCESSING], true)) {
            return;
        }

        DB::transaction(function () use ($withdrawal, $reason) {
            $withdrawal->update([
                'status' => Withdrawal::STATUS_CANCELLED,
                'rejection_reason' => $reason,
                'processed_at' => now(),
            ]);

            // Refund by creating a credit in the same funds group
            UserFunds::create([
                'user_id' => $withdrawal->user_id,
                'group' => 'referal',
                'type' => 'credit',
                'sum' => (float) $withdrawal->amount,
                'message' => "Withdrawal request #{$withdrawal->id} rejected - refund",
                'model' => Withdrawal::class,
                'model_id' => $withdrawal->id,
            ]);
        });
    }

    protected function markFailedAndRefund(Withdrawal $withdrawal, string $failureMessage): void
    {
        if ($withdrawal->status === Withdrawal::STATUS_FAILED) {
            return;
        }

        DB::transaction(function () use ($withdrawal, $failureMessage) {
            $withdrawal->update([
                'status' => Withdrawal::STATUS_FAILED,
                'failure_message' => $failureMessage,
                'processed_at' => now(),
            ]);

            // Avoid double-refund if we already created a credit for this withdrawal
            $alreadyRefunded = UserFunds::query()
                ->where('group', 'referal')
                ->where('type', 'credit')
                ->where('model', Withdrawal::class)
                ->where('model_id', $withdrawal->id)
                ->exists();

            if (!$alreadyRefunded) {
                UserFunds::create([
                    'user_id' => $withdrawal->user_id,
                    'group' => 'referal',
                    'type' => 'credit',
                    'sum' => (float) $withdrawal->amount,
                    'message' => "Withdrawal request #{$withdrawal->id} failed - refund",
                    'model' => Withdrawal::class,
                    'model_id' => $withdrawal->id,
                ]);
            }
        });
    }

    protected function notifyFailedOnce(Withdrawal $withdrawal, string $reason): void
    {
        $withdrawal->loadMissing('user');
        if (!$withdrawal->user) {
            return;
        }

        $key = 'email_withdrawal_failed_' . $withdrawal->id;
        if ($withdrawal->user->notifications()->where('group', $key)->exists()) {
            return;
        }

        try {
            Mail::to($withdrawal->user->email)->send(new WithdrawalIssue(
                user: $withdrawal->user,
                reason: $reason,
                settingsUrl: route('profile.settings'),
            ));
            $withdrawal->user->notifications()->create([
                'type' => 'info',
                'group' => $key,
                'message' => json_encode(['sent_at' => now()->toDateTimeString(), 'reason' => $reason]),
                'show' => 0,
                'closable' => 0,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to send withdrawal failed email', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function describeDestination(Withdrawal $withdrawal): ?string
    {
        $withdrawal->loadMissing('user');
        $user = $withdrawal->user;
        if (!$user) {
            return null;
        }

        try {
            $methods = $user->paymentMethods();
            foreach ($methods as $method) {
                if (($method->id ?? null) !== ($withdrawal->payout_method_id ?? null)) {
                    continue;
                }
                if (!empty($method->card)) {
                    $last4 = $method->card->last4 ?? 'XXXX';
                    $brand = ucfirst($method->card->brand ?? 'Card');
                    return "Bank account ending in {$last4} ({$brand})";
                }
                return 'Payout method: ' . ($method->id ?? '');
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return !empty($withdrawal->payout_method_id) ? ('Payout method: ' . $withdrawal->payout_method_id) : null;
    }
}

