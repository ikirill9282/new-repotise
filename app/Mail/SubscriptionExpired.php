<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\User;

class SubscriptionExpired extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Product $product,
        public ?string $expirationDateLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your TrekGuider Subscription to "' . $this->product->title . '" Has Expired',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_expired',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

