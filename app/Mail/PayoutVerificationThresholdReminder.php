<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PayoutVerificationThresholdReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $thresholdLabel = '$100',
        public ?string $actionUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Complete Full ID Verification to Keep Your Payouts Coming',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payout_verification_threshold_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

