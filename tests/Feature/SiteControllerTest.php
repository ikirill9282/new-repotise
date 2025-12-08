<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Page;
use App\Models\Product;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        Page::factory()->create(['slug' => 'home']);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_products_page_loads(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
    }

    public function test_product_detail_page_loads(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-product',
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $response = $this->get("/products/{$product->slug}");

        $response->assertStatus(200);
    }

    public function test_creators_page_loads(): void
    {
        $response = $this->get('/creators');

        $response->assertStatus(200);
    }

    public function test_search_page_loads(): void
    {
        $response = $this->get('/search');

        $response->assertStatus(200);
    }

    public function test_insights_page_loads(): void
    {
        $response = $this->get('/insights');

        $response->assertStatus(200);
    }

    public function test_article_detail_page_loads(): void
    {
        $user = $this->createUserWithoutEvents();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-article',
        ]);

        $response = $this->get("/insights/{$article->slug}");

        $response->assertStatus(200);
    }

    public function test_favorites_page_requires_authentication(): void
    {
        $response = $this->get('/favorites');

        $response->assertRedirect('/');
    }

    public function test_favorites_page_loads_for_authenticated_user(): void
    {
        $user = $this->createUserWithoutEvents();

        $response = $this->actingAs($user)->get('/favorites');

        $response->assertStatus(200);
    }

    public function test_sellers_page_loads(): void
    {
        $response = $this->get('/sellers');

        $response->assertStatus(200);
    }

    public function test_policies_page_loads(): void
    {
        $response = $this->get('/policies-all');

        $response->assertStatus(200);
    }
}
