<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Enums\Order as OrderEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::NEW,
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status_id' => OrderEnum::NEW,
        ]);
    }

    public function test_order_has_user_relationship(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->id, $order->user->id);
    }

    public function test_order_can_have_products(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['user_id' => $user->id]);

        $order->products()->attach($product->id, [
            'count' => 1,
            'price' => 100,
            'sale_price' => 0,
            'total' => 100,
            'total_without_discount' => 100,
        ]);

        $this->assertTrue($order->products()->exists());
        $this->assertEquals(1, $order->products()->count());
    }

    public function test_order_can_calculate_amount(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'price' => 100,
            'sale_price' => 10,
        ]);

        \App\Models\OrderProducts::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'price' => 100,
            'sale_price' => 10,
            'count' => 2,
        ]);

        $order->refresh();
        $order->load('order_products');
        $amount = $order->getAmount();

        $this->assertGreaterThan(0, $amount);
    }

    public function test_order_can_calculate_tax(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'cost' => 100,
        ]);

        $tax = $order->getTax();
        
        $this->assertIsFloat($tax);
        $this->assertGreaterThanOrEqual(0, $tax);
    }

    public function test_order_can_calculate_total(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'cost' => 100,
        ]);

        $total = $order->getTotal();
        
        $this->assertIsInt($total);
        $this->assertGreaterThanOrEqual(0, $total);
    }

    public function test_order_can_be_completed(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status_id' => OrderEnum::NEW,
        ]);

        $order->complete();

        $this->assertEquals(OrderEnum::PAID, $order->status_id);
    }

    public function test_order_can_check_if_free(): void
    {
        $user = $this->createUserWithoutEvents();
        $discount = \App\Models\Discount::withoutEvents(function () {
            return \App\Models\Discount::factory()->create(['author_id' => 0]);
        });
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'cost' => 0,
            'discount_id' => $discount->id,
        ]);

        $this->assertTrue($order->free());
    }

    public function test_order_can_get_costs(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'cost' => 100,
        ]);

        $costs = $order->getCosts();
        
        $this->assertIsArray($costs);
        $this->assertArrayHasKey('subtotal', $costs);
        $this->assertArrayHasKey('discount', $costs);
        $this->assertArrayHasKey('tax', $costs);
        $this->assertArrayHasKey('total', $costs);
    }
}
