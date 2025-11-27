<?php

namespace App\Mail;

use App\Enums\Action;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public string $trigger;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $seller,
        public float $amount,
        public ?string $donorName = null,
        public bool $anonymous = false,
        public ?string $message = null,
    ) {
        $this->trigger = Action::DONATION_RECEIVED;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You Received a Donation!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.donation_received',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

