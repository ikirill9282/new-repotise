<?php

namespace Tests\Unit\Jobs;

use App\Jobs\DeliveryGift;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class DeliveryGiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_gift_job_can_be_instantiated(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'recipient' => 'recipient@example.com',
        ]);

        $job = new DeliveryGift($order);

        $this->assertInstanceOf(DeliveryGift::class, $job);
        $this->assertEquals($order->id, $job->order->id);
    }

    public function test_delivery_gift_job_can_be_dispatched(): void
    {
        Queue::fake();

        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'recipient' => 'recipient@example.com',
        ]);

        DeliveryGift::dispatch($order);

        Queue::assertPushed(DeliveryGift::class, function ($job) use ($order) {
            return $job->order->id === $order->id;
        });
    }

    public function test_delivery_gift_job_has_unique_id(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'recipient' => 'recipient@example.com',
        ]);

        $job = new DeliveryGift($order);

        $this->assertEquals($order->id, $job->uniqueId());
    }
}
