<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\User;

class SubscriptionPaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Product $product,
        public string $amountPaidLabel,
        public string $billingPeriodLabel,
        public ?string $nextBillingDateLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your TrekGuider Subscription Payment for "' . $this->product->title . '" (Receipt)',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_payment_receipt',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

