<?php

namespace Tests\Unit\Events;

use App\Events\MailReset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class MailResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_reset_event_can_be_dispatched(): void
    {
        Event::fake();

        $user = $this->createUserWithoutEvents();

        event(new MailReset($user));

        Event::assertDispatched(MailReset::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    public function test_mail_reset_event_has_user(): void
    {
        $user = $this->createUserWithoutEvents();
        $event = new MailReset($user);

        $this->assertInstanceOf(User::class, $event->user);
        $this->assertEquals($user->id, $event->user->id);
    }
}
