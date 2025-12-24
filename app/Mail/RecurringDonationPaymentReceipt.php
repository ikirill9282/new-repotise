<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RecurringDonationPaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $donor,
        public User $seller,
        public string $amountPaidLabel,
        public string $currentPeriodLabel,
        public ?string $nextPaymentDateLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Recurring Donation Payment to ' . $this->seller->getName() . ' Was Successful (Receipt)',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recurring_donation_payment_receipt',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

