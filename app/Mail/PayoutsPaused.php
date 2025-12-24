<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PayoutsPaused extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Important Notice: Your TrekGuider Payouts Are Temporarily Paused',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payouts_paused',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

