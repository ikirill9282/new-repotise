<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_loads(): void
    {
        $response = $this->get('/payment/checkout');

        $response->assertStatus(200);
    }

    public function test_checkout_subscription_page_loads(): void
    {
        $response = $this->get('/payment/checkout-subscription');

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

    public function test_subscribe_requires_authentication(): void
    {
        $response = $this->get('/products/subscribe');

        $response->assertRedirect('/');
    }
}
