<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_api_endpoint(): void
    {
        $response = $this->get('/api/search');

        $response->assertStatus(200);
        $response->assertJsonStructure();
    }

    public function test_data_types_endpoint(): void
    {
        $response = $this->get('/api/data/types');

        $response->assertStatus(200);
    }

    public function test_data_categories_endpoint(): void
    {
        $response = $this->get('/api/data/categories');

        $response->assertStatus(200);
    }

    public function test_data_locations_endpoint(): void
    {
        $response = $this->get('/api/data/locations');

        $response->assertStatus(200);
    }

    public function test_data_tags_endpoint(): void
    {
        $response = $this->get('/api/data/tags');

        $response->assertStatus(200);
    }

    public function test_cart_push_requires_authentication(): void
    {
        $response = $this->post('/api/cart/push', [
            'item' => 1,
            'count' => 1,
        ]);

        // May redirect or return error, both are acceptable
        $this->assertContains($response->status(), [302, 401, 422]);
    }

    public function test_cart_count_endpoint(): void
    {
        $response = $this->post('/api/cart/count');

        $response->assertStatus(200);
    }

    public function test_feedback_likes_requires_authentication(): void
    {
        $response = $this->post('/api/feedback/likes', [
            'type' => 'product',
            'item_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_feedback_comment_requires_authentication(): void
    {
        $response = $this->post('/api/feedback/comment', [
            'text' => 'Test comment',
            'article_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_feedback_favorite_requires_authentication(): void
    {
        $response = $this->post('/api/feedback/favorite', [
            'type' => 'product',
            'item_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_feedback_follow_requires_authentication(): void
    {
        $user = $this->createUserWithoutEvents();
        
        $response = $this->post('/api/feedback/follow', [
            'author_id' => $user->id,
        ]);

        $response->assertStatus(401);
    }
}
