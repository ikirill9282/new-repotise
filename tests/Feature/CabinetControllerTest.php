<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CabinetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_requires_authentication(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/');
    }

    public function test_profile_loads_for_authenticated_user(): void
    {
        $user = $this->createUserWithoutEvents();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }

    public function test_public_profile_loads(): void
    {
        $user = $this->createUserWithoutEvents(['username' => 'testuser']);

        $response = $this->get("/profile/@{$user->username}");

        $response->assertStatus(200);
    }

    public function test_create_product_requires_authentication(): void
    {
        $response = $this->get('/profile/products/create');

        $response->assertRedirect('/');
    }

    public function test_create_product_loads_for_authenticated_user(): void
    {
        $user = $this->createUserWithoutEvents();

        $response = $this->actingAs($user)->get('/profile/products/create');

        $response->assertStatus(200);
    }

    public function test_orders_requires_authentication(): void
    {
        $response = $this->get('/profile/orders');

        $response->assertRedirect('/');
    }

    public function test_orders_loads_for_authenticated_user(): void
    {
        $user = $this->createUserWithoutEvents();

        $response = $this->actingAs($user)->get('/profile/orders');

        $response->assertStatus(200);
    }
}
