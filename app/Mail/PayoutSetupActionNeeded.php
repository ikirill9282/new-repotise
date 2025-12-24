<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PayoutSetupActionNeeded extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $requirements = [],
        public ?string $dueDateLabel = null,
        public ?string $actionUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Needed: Set Up Your Payout Information on TrekGuider',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payout_setup_action_needed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

