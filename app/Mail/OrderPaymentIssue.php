<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\User;

class OrderPaymentIssue extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?Order $order = null,
        public ?string $checkoutHash = null,
    ) {}

    public function envelope(): Envelope
    {
        $orderNumber = $this->order
            ? str_pad((string) $this->order->id, 6, '0', STR_PAD_LEFT)
            : null;

        $subjectOrder = $orderNumber ? " #{$orderNumber}" : '';

        return new Envelope(
            subject: "Heads Up: Payment Issue with Your TrekGuider Order{$subjectOrder}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_payment_issue',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

