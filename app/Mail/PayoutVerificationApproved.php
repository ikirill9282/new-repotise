<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PayoutVerificationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $dashboardUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re Verified! Your TrekGuider Account is Ready for Payouts!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payout_verification_approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

