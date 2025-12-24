<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\User;

class SubscriptionActive extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Product $product,
        public string $periodLabel,
        public string $priceLabel,
        public ?string $nextBillingDateLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re In! Your "' . $this->product->title . '" Subscription is Active!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_active',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

