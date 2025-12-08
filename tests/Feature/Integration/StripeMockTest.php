<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class StripeMockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockStripe();
    }

    public function test_stripe_configuration_is_mocked(): void
    {
        $this->assertEquals('sk_test_mock', config('cashier.secret'));
    }

    public function test_product_can_be_created_without_stripe_call(): void
    {
        $user = $this->createUserWithoutEvents();
        
        // This should not fail even if Stripe is not available
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Product',
        ]);

        $this->assertDatabaseHas('products', [
            'title' => 'Test Product',
            'user_id' => $user->id,
        ]);
    }
}
