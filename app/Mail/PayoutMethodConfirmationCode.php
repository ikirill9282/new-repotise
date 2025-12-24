<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PayoutMethodConfirmationCode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public int $code,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your TrekGuider Payout Method Confirmation Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payout_method_confirmation_code',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

