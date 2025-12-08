<?php

namespace Tests\E2E;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Services\Cart;
use App\Enums\Order as OrderEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class PaymentJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_payment_flow(): void
    {
        Queue::fake();

        $buyer = $this->createUserWithoutEvents();
        $seller = $this->createUserWithoutEvents();
        
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'price' => 100,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        // Add to cart
        $cart = new Cart();
        $cart->addProduct($product->id, 1);

        // Create order
        $order = Order::preparing($cart);
        $order->user_id = $buyer->id;
        $savedOrder = $order->savePrepared();

        // Access checkout page
        $response = $this->actingAs($buyer)->get('/payment/checkout');
        $response->assertStatus(200);

        // Verify order exists
        $this->assertDatabaseHas('orders', [
            'id' => $savedOrder->id,
            'user_id' => $buyer->id,
        ]);
    }

    public function test_gift_purchase_flow(): void
    {
        Queue::fake();

        $buyer = $this->createUserWithoutEvents();
        $seller = $this->createUserWithoutEvents();
        $recipient = 'gift@example.com';
        
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'price' => 50,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $cart = new Cart();
        $cart->addProduct($product->id, 1);

        $order = Order::preparing($cart);
        $order->user_id = $buyer->id;
        $order->recipient = $recipient;
        $order->gift = true;
        $savedOrder = $order->savePrepared();

        $this->assertDatabaseHas('orders', [
            'id' => $savedOrder->id,
            'recipient' => $recipient,
        ]);
    }
}
