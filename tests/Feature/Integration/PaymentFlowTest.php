<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('scout.driver', 'null');
    }

    public function test_checkout_page_loads_with_cart(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });

        $response = $this->actingAs($user)->get('/payment/checkout');
        
        $response->assertStatus(200);
    }

    public function test_payment_success_page_loads(): void
    {
        $response = $this->get('/payment/success');
        
        $response->assertStatus(200);
    }

    public function test_payment_error_page_loads(): void
    {
        $response = $this->get('/payment/error');
        
        $response->assertStatus(200);
    }
}
