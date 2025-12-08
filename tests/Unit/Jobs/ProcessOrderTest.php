<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessOrder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Enums\Order as OrderEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Mockery;

class ProcessOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_order_job_can_be_instantiated(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::NEW,
        ]);

        $job = new ProcessOrder($order);

        $this->assertInstanceOf(ProcessOrder::class, $job);
        $this->assertEquals($order->id, $job->order->id);
    }

    public function test_process_order_job_can_be_dispatched(): void
    {
        Queue::fake();

        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::PAID,
        ]);

        ProcessOrder::dispatch($order);

        Queue::assertPushed(ProcessOrder::class, function ($job) use ($order) {
            return $job->order->id === $order->id;
        });
    }

    public function test_process_order_job_has_unique_id(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::NEW,
        ]);

        $job = new ProcessOrder($order);

        $this->assertEquals($order->id, $job->uniqueId());
    }
}
