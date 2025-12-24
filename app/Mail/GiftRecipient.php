<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Order;
use App\Models\Gift;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Enums\Action;

class GiftRecipient extends Mailable
{
    use Queueable, SerializesModels;

    public string $trigger;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $recipient,
        public Order $order,
        public Gift $gift,
        public bool $isNewRecipient = false,
    )
    {
        $this->trigger = Action::GIFT_RECIPIENT;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $gifter = $this->order->buyer?->getName() ?? 'Someone';

        return new Envelope(
            subject: 'Surprise! ' . $gifter . ' Sent You a Gift on TrekGuider!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.gift_recipient',
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
