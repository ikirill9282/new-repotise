<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RecurringDonationPaymentIssue extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $donor,
        public User $seller,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Heads Up: Payment Issue with Your Recurring Donation to ' . $this->seller->getName(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recurring_donation_payment_issue',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

