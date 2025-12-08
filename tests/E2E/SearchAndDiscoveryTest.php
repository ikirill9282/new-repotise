<?php

namespace Tests\E2E;

use App\Models\User;
use App\Models\Product;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAndDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_search_and_discover_content(): void
    {
        $user = $this->createUserWithoutEvents();
        
        // Create content
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'title' => 'Amazing Travel Guide',
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $article = Article::factory()->create([
            'user_id' => $user->id,
            'title' => 'Travel Tips',
            'status_id' => \App\Enums\Status::ACTIVE,
        ]);

        // Search for products
        $response = $this->get('/search?q=Travel');
        $response->assertStatus(200);

        // Browse products
        $response = $this->get('/products');
        $response->assertStatus(200);

        // View product detail
        $response = $this->get("/products/{$product->slug}");
        $response->assertStatus(200);
        $response->assertSee($product->title);

        // Browse insights
        $response = $this->get('/insights');
        $response->assertStatus(200);

        // View article
        $response = $this->get("/insights/{$article->slug}");
        $response->assertStatus(200);
    }
}
