<?php

namespace Tests\Unit\Listeners;

use App\Events\ResetFailed;
use App\Listeners\ResetFails;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ResetFailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_fails_listener_is_registered(): void
    {
        Event::fake();

        $user = $this->createUserWithoutEvents();
        event(new ResetFailed($user, '123456', 'code'));

        Event::assertListening(ResetFailed::class, ResetFails::class);
    }
}
