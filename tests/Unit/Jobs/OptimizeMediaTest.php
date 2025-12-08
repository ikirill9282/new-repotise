<?php

namespace Tests\Unit\Jobs;

use App\Jobs\OptimizeMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class OptimizeMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_optimize_media_job_can_be_dispatched(): void
    {
        Queue::fake();
        
        OptimizeMedia::dispatch('public', 'test/path.jpg', []);

        Queue::assertPushed(OptimizeMedia::class);
    }

    public function test_optimize_media_job_can_be_instantiated(): void
    {
        $job = new OptimizeMedia('public', 'test/path.jpg', []);
        
        $this->assertInstanceOf(OptimizeMedia::class, $job);
    }
}
