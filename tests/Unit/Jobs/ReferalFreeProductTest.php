<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ReferalFreeProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class ReferalFreeProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_referal_free_product_job_can_be_instantiated(): void
    {
        $user = $this->createUserWithoutEvents();
        $job = new ReferalFreeProduct($user);

        $this->assertInstanceOf(ReferalFreeProduct::class, $job);
        $this->assertEquals($user->id, $job->user->id);
    }

    public function test_referal_free_product_job_can_be_dispatched(): void
    {
        Queue::fake();

        $user = $this->createUserWithoutEvents();
        ReferalFreeProduct::dispatch($user);

        Queue::assertPushed(ReferalFreeProduct::class, function ($job) use ($user) {
            return $job->user->id === $user->id;
        });
    }
}
