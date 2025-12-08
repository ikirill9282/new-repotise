<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Enums\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Product',
        ]);

        $this->assertDatabaseHas('products', [
            'title' => 'Test Product',
            'user_id' => $user->id,
        ]);
    }

    public function test_product_slug_is_auto_generated(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Product Title',
            'slug' => null,
        ]);

        $this->assertNotNull($product->slug);
        $this->assertNotEmpty($product->slug);
    }

    public function test_product_has_author_relationship(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $product->author);
        $this->assertEquals($user->id, $product->author->id);
    }

    public function test_product_can_calculate_price(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'price' => 100,
            'sale_price' => 20,
        ]);

        $this->assertEquals(80, $product->getPrice());
    }

    public function test_product_can_get_price_without_discount(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'price' => 100,
            'sale_price' => 0,
        ]);

        $this->assertNull($product->getPriceWithoutDiscount());
    }

    public function test_product_has_url(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-product',
        ]);

        $url = $product->makeUrl();
        
        $this->assertStringContainsString('test-product', $url);
        $this->assertStringContainsString('products', $url);
    }

    public function test_product_can_have_categories(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create(['user_id' => $user->id]);
        $category = \App\Models\Category::factory()->create();

        $product->categories()->attach($category->id);

        $this->assertTrue($product->categories()->exists());
        // Count may include categories from TestSeeder
        $this->assertGreaterThanOrEqual(1, $product->categories()->count());
    }

    public function test_product_can_have_reviews(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create(['user_id' => $user->id]);
        $review = \App\Models\Review::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($product->reviews()->exists());
        $this->assertEquals(1, $product->reviews()->count());
    }

    public function test_product_can_check_if_has_orders(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $this->assertFalse($product->hasOrders());

        $order = \App\Models\Order::factory()->create(['user_id' => $user->id]);
        \App\Models\OrderProducts::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $this->assertTrue($product->hasOrders());
    }

    public function test_product_prepares_rating_images(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $images = $product->prepareRatingImages();
        
        $this->assertNotEmpty($images);
    }
}
