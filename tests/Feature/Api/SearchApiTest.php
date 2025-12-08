<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class SearchApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('scout.driver', 'null');
    }

    public function test_search_api_returns_results(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
        Product::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Product',
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $response = $this->get('/api/search?q=Test');
        
        $response->assertStatus(200);
    }

    public function test_search_api_handles_empty_query(): void
    {
        $response = $this->get('/api/search');
        
        $response->assertStatus(200);
    }
}
