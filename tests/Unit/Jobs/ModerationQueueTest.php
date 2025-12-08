<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ModerationQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class ModerationQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_moderation_queue_job_can_be_dispatched(): void
    {
        Queue::fake();

        ModerationQueue::dispatch();

        Queue::assertPushed(ModerationQueue::class);
    }

    public function test_moderation_queue_job_can_be_instantiated(): void
    {
        $job = new ModerationQueue();
        
        $this->assertInstanceOf(ModerationQueue::class, $job);
    }
}
