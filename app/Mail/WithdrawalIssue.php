<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WithdrawalIssue extends Mailable
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
            subject: 'Heads Up: Issue with Your TrekGuider Withdrawal Request',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal_issue',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

