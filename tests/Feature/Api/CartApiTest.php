<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class CartApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('scout.driver', 'null');
    }

    public function test_can_add_to_cart_via_api(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
        $seller = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/api/cart/push', [
            'item' => $product->id,
            'count' => 1,
        ]);

        $response->assertStatus(200);
    }

    public function test_can_get_cart_count(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });

        $response = $this->actingAs($user)->post('/api/cart/count');
        
        $response->assertStatus(200);
    }

    public function test_can_remove_from_cart(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
        $seller = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/api/cart/remove', [
            'item' => $product->id,
        ]);

        $response->assertStatus(200);
    }
}
