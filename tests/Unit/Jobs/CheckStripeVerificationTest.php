<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CheckStripeVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class CheckStripeVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_stripe_verification_job_can_be_dispatched(): void
    {
        Queue::fake();

        $user = $this->createUserWithoutEvents();
        CheckStripeVerification::dispatch($user);

        Queue::assertPushed(CheckStripeVerification::class);
    }
}
