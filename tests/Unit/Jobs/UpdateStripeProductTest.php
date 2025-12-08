<?php

namespace Tests\Unit\Jobs;

use App\Jobs\UpdateStripeProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class UpdateStripeProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_stripe_product_job_can_be_dispatched(): void
    {
        Queue::fake();

        UpdateStripeProduct::dispatch();

        Queue::assertPushed(UpdateStripeProduct::class);
    }

    public function test_update_stripe_product_job_can_be_instantiated(): void
    {
        $job = new UpdateStripeProduct();
        
        $this->assertInstanceOf(UpdateStripeProduct::class, $job);
    }
}
