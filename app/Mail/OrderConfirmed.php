<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\User;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        $orderNumber = str_pad((string) $this->order->id, 6, '0', STR_PAD_LEFT);

        return new Envelope(
            subject: "Your TrekGuider Order #{$orderNumber} is Confirmed! (Access Your Goodies)",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_confirmed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

