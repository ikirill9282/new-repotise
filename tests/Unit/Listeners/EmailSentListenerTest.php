<?php

namespace Tests\Unit\Listeners;

use App\Listeners\EmailSentListener;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class EmailSentListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_sent_listener_is_registered(): void
    {
        Event::fake();

        Event::assertListening(MessageSent::class, EmailSentListener::class);
    }
}
