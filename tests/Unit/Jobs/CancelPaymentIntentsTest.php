<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CancelPaymentIntents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class CancelPaymentIntentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancel_payment_intents_job_can_be_dispatched(): void
    {
        Queue::fake();

        CancelPaymentIntents::dispatch(['pi_test_123']);

        Queue::assertPushed(CancelPaymentIntents::class);
    }

    public function test_cancel_payment_intents_job_can_be_instantiated(): void
    {
        $job = new CancelPaymentIntents(['pi_test_123']);
        
        $this->assertInstanceOf(CancelPaymentIntents::class, $job);
    }
}
