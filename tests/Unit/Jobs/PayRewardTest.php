<?php

namespace Tests\Unit\Jobs;

use App\Jobs\PayReward;
use App\Models\Order;
use App\Models\User;
use App\Enums\Order as OrderEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class PayRewardTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_reward_job_can_be_instantiated(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::REWARDING,
        ]);

        $job = new PayReward($order);

        $this->assertInstanceOf(PayReward::class, $job);
        $this->assertEquals($order->id, $job->model->id);
    }

    public function test_pay_reward_job_can_be_dispatched(): void
    {
        Queue::fake();

        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::REWARDING,
        ]);

        PayReward::dispatch($order);

        Queue::assertPushed(PayReward::class, function ($job) use ($order) {
            return $job->model->id === $order->id;
        });
    }

    public function test_pay_reward_job_has_unique_id(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::REWARDING,
        ]);

        $job = new PayReward($order);

        $this->assertEquals($order->id, $job->uniqueId());
    }
}
