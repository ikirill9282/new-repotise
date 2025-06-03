<?php

namespace App\Listeners;

use App\Models\MailLog;
use Illuminate\Mail\Events\MessageSent;


class EmailSentListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
      // dd($event->message->getId());
      $data = $event->data;
      $mail = $data['message']->getSymfonyMessage();
      $sent = $event->sent->getSymfonySentMessage();
      // dd($mail);

      $recipient = !array_key_exists('user', $data) 
        ? $data['user']->email
        : ((empty($mail->getTo())) ? 'Unknown' : $mail->getTo()[0]->getAddress());

      $data = [
        'message_id' => preg_replace('/^\<(.*?)@.*$/is', "$1", $sent->getMessageId()),
        'recipient' => $recipient,
        'subject' => $mail->getSubject(),
        'trigger' => $data['trigger'],
      ];

      MailLog::create($data);
    }
}
