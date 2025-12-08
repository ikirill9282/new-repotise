<?php

namespace Tests\E2E;

use App\Models\User;
use App\Models\Product;
use App\Services\Cart;
use App\Enums\Order as OrderEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;

class CompleteUserJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_user_registration_to_purchase_flow(): void
    {
        Queue::fake();
        Event::fake();

        // 1. User registration
        $buyer = $this->createUserWithoutEvents([
            'email' => 'buyer@example.com',
            'email_verified_at' => now(),
        ]);

        $seller = $this->createUserWithoutEvents([
            'email' => 'seller@example.com',
            'email_verified_at' => now(),
        ]);

        // 2. Seller creates product
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'price' => 100,
            'sale_price' => 10,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'user_id' => $seller->id,
        ]);

        // 3. Buyer browses products
        $response = $this->actingAs($buyer)->get('/products');
        $response->assertStatus(200);

        // 4. Buyer views product detail
        $response = $this->actingAs($buyer)->get("/products/{$product->slug}");
        $response->assertStatus(200);
        $response->assertSee($product->title);

        // 5. Buyer adds product to cart
        $cart = new Cart();
        $cart->addProduct($product->id, 1);
        $this->assertTrue($cart->hasProducts());

        // 6. Buyer goes to checkout
        $order = Order::preparing($cart);
        $order->user_id = $buyer->id;
        $savedOrder = $order->savePrepared();

        $this->assertDatabaseHas('orders', [
            'id' => $savedOrder->id,
            'user_id' => $buyer->id,
            'status_id' => OrderEnum::NEW,
        ]);

        // 7. Order completion
        $savedOrder->complete();
        $this->assertEquals(OrderEnum::PAID, $savedOrder->status_id);
    }

    public function test_complete_creator_journey(): void
    {
        // 1. User registers as creator
        $creator = $this->createUserWithoutEvents([
            'email' => 'creator@example.com',
            'email_verified_at' => now(),
        ]);

        // 2. Creator accesses profile
        $response = $this->actingAs($creator)->get('/profile');
        $response->assertStatus(200);

        // 3. Creator creates product
        $response = $this->actingAs($creator)->get('/profile/products/create');
        $response->assertStatus(200);

        // 4. Creator creates article
        $response = $this->actingAs($creator)->get('/profile/articles/create');
        $response->assertStatus(200);

        // 5. Creator views analytics
        $response = $this->actingAs($creator)->get('/profile/analytics');
        $response->assertStatus(200);
    }
}
