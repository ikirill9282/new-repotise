<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Category;
use App\Models\Type;
use App\Models\Location;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_data_endpoint_returns_empty_array(): void
    {
        $response = $this->getJson('/api/data');
        
        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function test_tags_endpoint_returns_tags(): void
    {
        // Tags may already exist from TestSeeder, so we use firstOrCreate
        Tag::firstOrCreate(['slug' => 'travel'], ['title' => 'Travel']);
        Tag::firstOrCreate(['slug' => 'adventure'], ['title' => 'Adventure']);

        $response = $this->getJson('/api/data/tags');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'slug']
            ]);
    }

    public function test_types_endpoint_returns_types(): void
    {
        $response = $this->getJson('/api/data/types');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'slug']
            ]);
    }

    public function test_locations_endpoint_returns_locations(): void
    {
        $response = $this->getJson('/api/data/locations');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'slug']
            ]);
    }

    public function test_categories_endpoint_returns_categories(): void
    {
        $response = $this->getJson('/api/data/categories');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'slug']
            ]);
    }

    public function test_messages_endpoint_requires_validation(): void
    {
        $response = $this->postJson('/api/data/messages', []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_messages_endpoint_accepts_valid_data(): void
    {
        $response = $this->postJson('/api/data/messages', [
            'message' => 'Test message',
            'email' => 'test@example.com',
        ]);
        
        // Should return success or redirect
        $this->assertContains($response->status(), [200, 201, 302]);
    }

    public function test_favorite_author_endpoint_requires_auth(): void
    {
        $response = $this->postJson('/api/data/favorite-author', [
            'author_id' => 1,
        ]);
        
        // Should require authentication
        $this->assertContains($response->status(), [401, 403, 302]);
    }

    public function test_upload_image_endpoint_requires_auth(): void
    {
        $response = $this->postJson('/api/data/upload-image', []);
        
        // Should require authentication
        $this->assertContains($response->status(), [401, 403, 302]);
    }
}
