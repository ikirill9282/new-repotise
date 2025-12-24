<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class RecurringDonationCanceled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $donor,
        public User $seller,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation: Your Recurring Donation to ' . $this->seller->getName() . ' Has Been Canceled',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recurring_donation_canceled',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

