<?php

namespace Tests\Unit\Events;

use App\Events\ResetFailed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ResetFailedTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_failed_event_can_be_dispatched(): void
    {
        Event::fake();

        $user = $this->createUserWithoutEvents();
        event(new ResetFailed($user, '123456', 'code'));

        Event::assertDispatched(ResetFailed::class);
    }

    public function test_reset_failed_event_with_different_reasons(): void
    {
        Event::fake();

        $user = $this->createUserWithoutEvents();
        
        event(new ResetFailed($user, '123456', 'user'));
        event(new ResetFailed($user, '123456', 'code'));
        event(new ResetFailed($user, '123456', 'invalid'));

        Event::assertDispatched(ResetFailed::class, 3);
    }
}
