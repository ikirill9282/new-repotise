<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\CardExpiringSoon;

class NotifyExpiringSubscriptionCards extends Command
{
    protected $signature = 'app:notify-expiring-subscription-cards {--days=30}';

    protected $description = 'Send subscription card expiring soon emails';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $days = $days > 0 ? $days : 30;

        $cutoff = Carbon::now()->addDays($days);

        $users = User::query()
            ->whereNotNull('stripe_id')
            ->whereHas('subscriptions', function ($q) {
                $q->where('stripe_status', 'active');
            })
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            try {
                $paymentMethods = $user->paymentMethods();
            } catch (\Throwable $e) {
                Log::warning('Failed to fetch payment methods for expiring card check', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            foreach ($paymentMethods as $pm) {
                if (($pm->type ?? null) !== 'card' || empty($pm->card)) {
                    continue;
                }

                $expMonth = (int) ($pm->card->exp_month ?? 0);
                $expYear = (int) ($pm->card->exp_year ?? 0);
                $last4 = (string) ($pm->card->last4 ?? 'XXXX');

                if ($expMonth < 1 || $expMonth > 12 || $expYear < 2000) {
                    continue;
                }

                $expDate = Carbon::create($expYear, $expMonth, 1)->endOfMonth();
                if ($expDate->isPast() || $expDate->greaterThan($cutoff)) {
                    continue;
                }

                // Avoid spamming: once per card per expiration month.
                $key = 'card_expiring_notice_' . ($pm->id ?? '') . '_' . $expYear . '_' . $expMonth;
                $already = $user->notifications()
                    ->where('group', $key)
                    ->exists();

                if ($already) {
                    continue;
                }

                $expLabel = $expDate->format('m.d.Y');

                Mail::to($user->email)->send(new CardExpiringSoon($user, $last4, $expLabel));

                // Mark as sent using user_notifications (no extra tables).
                $user->notifications()->create([
                    'type' => 'info',
                    'group' => $key,
                    'message' => json_encode([
                        'heading' => 'Card Expiring Soon',
                        'message' => "Expiring card notice sent for **** {$last4} ({$expMonth}/{$expYear}).",
                        'icon' => 'info',
                    ]),
                    'show' => 0,
                    'closable' => 0,
                ]);

                $sent++;
                break; // One email per user per run.
            }
        }

        $this->info("Sent {$sent} expiring card reminder(s).");

        return Command::SUCCESS;
    }
}

