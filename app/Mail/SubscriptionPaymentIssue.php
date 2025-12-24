<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\User;

class SubscriptionPaymentIssue extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Product $product,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Heads Up: Payment Issue with Your "' . $this->product->title . '" Subscription',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_payment_issue',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

