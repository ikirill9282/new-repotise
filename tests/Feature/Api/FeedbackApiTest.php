<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Product;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_views_endpoint_requires_auth(): void
    {
        $response = $this->getJson('/api/feedback/views');
        
        $this->assertContains($response->status(), [401, 403, 302]);
    }

    public function test_likes_endpoint_requires_auth(): void
    {
        $response = $this->postJson('/api/feedback/likes', [
            'item_id' => 1,
            'type' => 'product',
        ]);
        
        $this->assertContains($response->status(), [401, 403, 302]);
    }

    public function test_likes_endpoint_validates_data(): void
    {
        $user = $this->createUserWithoutEvents();
        
        $response = $this->actingAs($user)->postJson('/api/feedback/likes', []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['item_id', 'type']);
    }

    public function test_comment_endpoint_requires_auth(): void
    {
        $response = $this->postJson('/api/feedback/comment', [
            'text' => 'Test comment',
        ]);
        
        $this->assertContains($response->status(), [401, 403, 302]);
    }

    public function test_comment_endpoint_validates_data(): void
    {
        $user = $this->createUserWithoutEvents();
        
        $response = $this->actingAs($user)->postJson('/api/feedback/comment', []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['text']);
    }

    public function test_review_endpoint_requires_auth(): void
    {
        $response = $this->postJson('/api/feedback/review', [
            'rating' => 5,
            'text' => 'Great product!',
        ]);
        
        $this->assertContains($response->status(), [401, 403, 302]);
    }

    public function test_review_endpoint_validates_data(): void
    {
        $user = $this->createUserWithoutEvents();
        
        $response = $this->actingAs($user)->postJson('/api/feedback/review', []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    public function test_favorite_endpoint_requires_auth(): void
    {
        $response = $this->postJson('/api/feedback/favorite', [
            'item_id' => 1,
            'type' => 'product',
        ]);
        
        $this->assertContains($response->status(), [401, 403, 302]);
    }

    public function test_follow_endpoint_requires_auth(): void
    {
        $response = $this->postJson('/api/feedback/follow', [
            'user_id' => 1,
        ]);
        
        $this->assertContains($response->status(), [401, 403, 302]);
    }
}
