<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ReferalPromocode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class ReferalPromocodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_referal_promocode_job_can_be_instantiated(): void
    {
        $user = $this->createUserWithoutEvents();
        $job = new ReferalPromocode($user);

        $this->assertInstanceOf(ReferalPromocode::class, $job);
        $this->assertEquals($user->id, $job->user->id);
    }

    public function test_referal_promocode_job_can_be_dispatched(): void
    {
        Queue::fake();

        $user = $this->createUserWithoutEvents();
        ReferalPromocode::dispatch($user);

        Queue::assertPushed(ReferalPromocode::class, function ($job) use ($user) {
            return $job->user->id === $user->id;
        });
    }
}
