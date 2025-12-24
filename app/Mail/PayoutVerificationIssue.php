<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PayoutVerificationIssue extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $actionUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Important: Action Required for Your TrekGuider Account Verification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payout_verification_issue',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

