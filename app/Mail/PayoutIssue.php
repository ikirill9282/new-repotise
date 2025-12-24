<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PayoutIssue extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $reason,
        public ?string $settingsUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Heads Up: Issue with Your Recent TrekGuider Payout',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payout_issue',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

