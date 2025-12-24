<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\User;

class SubscriptionCanceled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Product $product,
        public ?string $endOfAccessDateLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation: Your Subscription to "' . $this->product->title . '" Has Been Canceled',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_canceled',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

