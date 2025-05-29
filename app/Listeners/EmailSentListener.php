<?php

namespace App\Listeners;

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
        // dd($event);
    }
}
