<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PayoutOnItsWay extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $amountLabel,
        public ?string $expectedArrivalDateLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Good News! Your TrekGuider Payout is On Its Way!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payout_on_its_way',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

