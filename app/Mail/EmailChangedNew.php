<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EmailChangedNew extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your TrekGuider Email Address Has Been Updated',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.email_changed_new',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

