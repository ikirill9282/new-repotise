<?php

namespace App\Listeners;

use App\Events\MailVerify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MailVerifyListener
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
    public function handle(MailVerify $event): void
    {
        // dd($event);
    }
}
