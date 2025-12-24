<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EmailChangedOld extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $newEmail,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Security Alert: Your TrekGuider Email Was Changed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.email_changed_old',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

