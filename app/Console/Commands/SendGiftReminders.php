<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GiftReminderRecipient;
use App\Models\Gift;

class SendGiftReminders extends Command
{
    protected $signature = 'app:send-gift-reminders {--days=3}';

    protected $description = 'Send reminder emails for unclaimed gifts';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $days = $days > 0 ? $days : 3;

        $cutoff = Carbon::now()->subDays($days);

        $gifts = Gift::query()
            ->where('status', Gift::STATUS_CREATED)
            ->whereNull('claimed_at')
            ->whereNull('reminder_sent_at')
            ->where('created_at', '<=', $cutoff)
            ->with(['order.order_products.product', 'order.buyer'])
            ->limit(200)
            ->get();

        $sent = 0;

        foreach ($gifts as $gift) {
            try {
                if (!$gift->order || empty($gift->recipient_email)) {
                    continue;
                }

                Mail::to($gift->recipient_email)->send(new GiftReminderRecipient($gift->order, $gift));
                $gift->update(['reminder_sent_at' => Carbon::now()]);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Failed to send gift reminder', [
                    'gift_id' => $gift->id,
                    'order_id' => $gift->order_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Sent {$sent} gift reminder(s).");

        return Command::SUCCESS;
    }
}

