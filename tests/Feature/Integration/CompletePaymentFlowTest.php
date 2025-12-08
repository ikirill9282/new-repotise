<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Services\Cart;
use App\Enums\Order as OrderEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;

class CompletePaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_order_to_payment_flow(): void
    {
        Queue::fake();
        Event::fake();

        // Create users
        $buyer = $this->createUserWithoutEvents();
        $seller = $this->createUserWithoutEvents();
        
        // Create product
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'price' => 100,
            'sale_price' => 10,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        // Add to cart
        $cart = new Cart();
        $cart->addProduct($product->id, 1);
        
        $this->assertTrue($cart->hasProducts());

        // Create order
        $order = Order::preparing($cart);
        $order->user_id = $buyer->id;
        $savedOrder = $order->savePrepared();
        
        $this->assertDatabaseHas('orders', [
            'id' => $savedOrder->id,
            'user_id' => $buyer->id,
            'status_id' => OrderEnum::NEW,
        ]);

        // Complete order
        $savedOrder->complete();
        
        $this->assertEquals(OrderEnum::PAID, $savedOrder->status_id);
    }

    public function test_order_calculation_flow(): void
    {
        $buyer = $this->createUserWithoutEvents();
        $seller = $this->createUserWithoutEvents();
        
        $product1 = Product::factory()->create([
            'user_id' => $seller->id,
            'price' => 100,
            'sale_price' => 20,
        ]);

        $product2 = Product::factory()->create([
            'user_id' => $seller->id,
            'price' => 50,
            'sale_price' => 5,
        ]);

        $cart = new Cart();
        $cart->addProduct($product1->id, 2);
        $cart->addProduct($product2->id, 1);
        
        $order = Order::preparing($cart);
        $order->user_id = $buyer->id;
        
        $amount = $order->getAmount();
        $tax = $order->getTax();
        $total = $order->getTotal();
        
        $this->assertGreaterThan(0, $amount);
        $this->assertGreaterThanOrEqual(0, $tax);
        $this->assertGreaterThan(0, $total);
        $this->assertEquals($amount - $order->getDiscount() + $tax, $total);
    }
}
