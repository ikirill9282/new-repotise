<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Order;
use App\Enums\Action;

class InviteByPurchase extends Mailable
{
  use Queueable, SerializesModels;


  public string $trigger;
  /**
   * Create a new message instance.
   */
  public function __construct(
    public User $user,
    public Order $order,
    public string $password,
  ) {
    $this->trigger = Action::INVITE_BY_PURCHASE;
  }

  public function build()
  {
    return $this->subject('Welcome to TrekGuider.com!')
      ->markdown('emails.invite_by_purchase')
      ->with([
        'user' => $this->user,
        'order' => $this->order,
      ]);
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Invite By Purchase',
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.invite_by_purchase',
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
