<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WithdrawalProcessing extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $amountLabel,
        public ?string $destinationLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your TrekGuider Withdrawal is Being Processed!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal_processing',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

