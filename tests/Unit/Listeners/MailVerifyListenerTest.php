<?php

namespace Tests\Unit\Listeners;

use App\Events\MailVerify;
use App\Listeners\MailVerifyListener;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class MailVerifyListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_verify_listener_is_registered(): void
    {
        Event::fake();

        $user = $this->createUserWithoutEvents();
        event(new MailVerify($user));

        Event::assertListening(MailVerify::class, MailVerifyListener::class);
    }
}
