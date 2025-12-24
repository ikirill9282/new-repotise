<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class CardExpiringSoon extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $last4,
        public string $expirationDateLabel,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Friendly Reminder: Your Card for TrekGuider Subscriptions is Expiring Soon',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.card_expiring_soon',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

