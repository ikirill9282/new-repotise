<?php

namespace App\Services;

use App\Models\Payout;
use App\Models\RevenueShare;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;

class StripePayoutProcessor
{
    public function process(Payout $payout): Payout
    {
        if ($payout->status !== Payout::STATUS_PENDING) {
            throw new \Exception('This payout has already been processed.');
        }

        $payout->loadMissing('user');

        if (!$payout->user) {
            throw new \Exception('Payout user not found.');
        }

        $amount = (float) $payout->amount;
        if ($amount <= 0) {
            throw new \Exception('Payout amount must be greater than zero.');
        }

        $amountInCents = (int) round($amount * 100);
        $currency = strtolower($payout->currency ?? 'usd');

        try {
            // Create payout in Stripe
            // Note: For standard Stripe accounts (not Connect)
            // If Stripe Connect is needed later, add stripe_account parameter
            $stripePayout = Cashier::stripe()->payouts->create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'metadata' => [
                    'payout_id' => $payout->id,
                    'user_id' => $payout->user_id,
                ],
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe payout creation failed', [
                'payout_id' => $payout->id,
                'error' => $e->getMessage(),
            ]);
            
            throw new \Exception('Failed to create payout in Stripe: ' . $e->getMessage());
        }

        return DB::transaction(function () use ($payout, $stripePayout, $amount) {
            // Update payout status
            $payout->forceFill([
                'status' => Payout::STATUS_PROCESSING,
                'stripe_payout_id' => $stripePayout->id,
                'failure_message' => null,
            ])->save();

            // Link revenue shares to this payout
            // This should be done when creating the payout, but we can also do it here
            // For now, we'll assume revenue shares are linked when payout is created
            
            // Debit user's balance
            $payout->user->decrement('balance', $amount);

            return $payout->refresh();
        });
    }

    /**
     * Update payout status from Stripe webhook
     */
    public function updateStatusFromStripe(string $stripePayoutId, string $status): void
    {
        $payout = Payout::where('stripe_payout_id', $stripePayoutId)->first();
        
        if (!$payout) {
            Log::warning('Payout not found for Stripe payout ID', ['stripe_payout_id' => $stripePayoutId]);
            return;
        }

        $payout->update([
            'status' => match($status) {
                'paid' => Payout::STATUS_COMPLETED,
                'failed', 'canceled' => Payout::STATUS_FAILED,
                'pending', 'in_transit' => Payout::STATUS_PROCESSING,
                default => $payout->status,
            },
            'processed_at' => now(),
        ]);
    }
}
