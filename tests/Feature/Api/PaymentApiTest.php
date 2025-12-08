<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Order;
use App\Enums\Order as OrderEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_confirm_endpoint_exists(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::NEW,
        ]);

        $response = $this->postJson('/api/payment/confirm', [
            'order_id' => $order->id,
        ]);

        // Should process or return validation error
        $this->assertContains($response->status(), [200, 201, 302, 422]);
    }

    public function test_payment_confirm_validates_order_id(): void
    {
        $response = $this->postJson('/api/payment/confirm', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id']);
    }
}
