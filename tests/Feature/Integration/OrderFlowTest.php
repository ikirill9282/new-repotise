<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Services\Cart;
use App\Enums\Order as OrderEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('scout.driver', 'null');
    }

    public function test_complete_order_flow(): void
    {
        // Create user and product
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
        $seller = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
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
        $this->assertEquals(1, $cart->getCartCount());

        // Create order from cart
        $order = Order::preparing($cart);
        $order->user_id = $user->id;
        $savedOrder = $order->savePrepared();
        
        $this->assertDatabaseHas('orders', [
            'id' => $savedOrder->id,
            'user_id' => $user->id,
            'status_id' => OrderEnum::NEW,
        ]);

        // Verify order products
        $this->assertTrue($savedOrder->products()->exists());
        $this->assertEquals(1, $savedOrder->products()->count());
    }

    public function test_order_calculation(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
        $seller = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'price' => 100,
            'sale_price' => 20,
        ]);

        $cart = new Cart();
        $cart->addProduct($product->id, 2);
        
        $order = Order::preparing($cart);
        $order->user_id = $user->id;
        
        $amount = $order->getAmount();
        $this->assertGreaterThan(0, $amount);
    }
}
