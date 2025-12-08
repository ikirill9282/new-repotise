<?php

namespace Tests\Unit\Listeners;

use App\Events\MailReset;
use App\Listeners\SendMailReset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class SendMailResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_mail_reset_listener_is_registered(): void
    {
        Event::fake();

        $user = $this->createUserWithoutEvents();
        event(new MailReset($user));

        Event::assertListening(MailReset::class, SendMailReset::class);
    }
}
