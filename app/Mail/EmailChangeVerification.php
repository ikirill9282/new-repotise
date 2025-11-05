<?php

namespace App\Mail;

use App\Enums\Action;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailChangeVerification extends Mailable
{
    use Queueable, SerializesModels;

    public string $trigger;

    public function __construct(
        public User $user,
        public string $verificationUrl
    ) {
        $this->trigger = Action::VERIFY_EMAIL;
    }

    public function build(): self
    {
        return $this
            ->subject('Confirm your new email address')
            ->view('emails.change_email');
    }
}
