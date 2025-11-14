<?php

namespace App\Mail;

use App\Enums\Action;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletionCode extends Mailable
{
    use Queueable, SerializesModels;

    public string $trigger;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $code
    ) {
        $this->trigger = Action::ACCOUNT_DELETION_CODE;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm Account Deletion',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.delete_account_code',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

