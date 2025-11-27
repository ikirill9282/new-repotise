<?php

namespace App\Mail;

use App\Enums\Action;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationSent extends Mailable
{
    use Queueable, SerializesModels;

    public string $trigger;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $donor,
        public User $seller,
        public float $amount,
        public bool $monthlySupport = false,
    ) {
        $this->trigger = Action::DONATION_SENT;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->monthlySupport 
                ? 'Monthly Support Subscription Confirmed' 
                : 'Donation Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.donation_sent',
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

