<?php

namespace Tests\Performance;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_performance(): void
    {
        $start = microtime(true);
        
        for ($i = 0; $i < 10; $i++) {
            $this->createUserWithoutEvents();
        }
        
        $duration = microtime(true) - $start;
        
        // Should create 10 users in less than 1 second
        $this->assertLessThan(1.0, $duration, "User creation took {$duration} seconds");
    }

    public function test_product_creation_performance(): void
    {
        $user = $this->createUserWithoutEvents();
        
        $start = microtime(true);
        
        for ($i = 0; $i < 10; $i++) {
            Product::factory()->create(['user_id' => $user->id]);
        }
        
        $duration = microtime(true) - $start;
        
        // Should create 10 products in less than 2 seconds
        $this->assertLessThan(2.0, $duration, "Product creation took {$duration} seconds");
    }

    public function test_order_calculation_performance(): void
    {
        $user = $this->createUserWithoutEvents();
        $order = Order::factory()->create(['user_id' => $user->id]);
        
        $start = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $order->getAmount();
            $order->getTax();
            $order->getTotal();
        }
        
        $duration = microtime(true) - $start;
        
        // Should calculate 100 times in less than 0.5 seconds
        $this->assertLessThan(0.5, $duration, "Order calculation took {$duration} seconds");
    }

    public function test_database_query_performance(): void
    {
        $user = $this->createUserWithoutEvents();
        
        // Create some products
        for ($i = 0; $i < 20; $i++) {
            Product::factory()->create(['user_id' => $user->id]);
        }
        
        $start = microtime(true);
        
        $products = Product::where('user_id', $user->id)->get();
        
        $duration = microtime(true) - $start;
        
        // Should query 20 products in less than 0.1 seconds
        $this->assertLessThan(0.1, $duration, "Database query took {$duration} seconds");
        $this->assertCount(20, $products);
    }
}
