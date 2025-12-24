<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PayoutVerificationThresholdReminder;
use App\Models\User;

class RemindPayoutVerificationThreshold extends Command
{
    protected $signature = 'app:remind-payout-verification-threshold {--threshold=100}';

    protected $description = 'Email creators to complete payout verification after reaching earnings threshold';

    public function handle(): int
    {
        $threshold = (float) $this->option('threshold');
        $threshold = $threshold > 0 ? $threshold : 100;

        $users = User::query()
            ->where('verified', 0)
            ->where('balance', '>=', $threshold)
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                if (!$user->hasRole('creator') && !$user->hasRole('seller')) {
                    continue;
                }

                $key = 'email_payout_verification_threshold_' . (int) $threshold;
                if ($user->notifications()->where('group', $key)->exists()) {
                    continue;
                }

                Mail::to($user->email)->send(new PayoutVerificationThresholdReminder(
                    user: $user,
                    thresholdLabel: '$' . number_format($threshold, 0),
                    actionUrl: $user->makeProfileVerificationUrl(),
                ));

                $user->notifications()->create([
                    'type' => 'info',
                    'group' => $key,
                    'message' => json_encode(['sent_at' => now()->toDateTimeString()]),
                    'show' => 0,
                    'closable' => 0,
                ]);

                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Failed to send payout verification threshold reminder', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Sent {$sent} threshold reminder(s).");

        return Command::SUCCESS;
    }
}

