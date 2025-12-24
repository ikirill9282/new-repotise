<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Gift;
use App\Models\Order;

class GiftReminderRecipient extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public Gift $gift,
    ) {}

    public function envelope(): Envelope
    {
        $gifter = $this->order->buyer?->getName() ?? 'Someone';
        return new Envelope(
            subject: 'Friendly Nudge: Your TrekGuider Gift from ' . $gifter . ' is Waiting!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.gift_reminder_recipient',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

