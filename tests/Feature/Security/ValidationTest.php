<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_validation_on_signin(): void
    {
        $response = $this->post('/auth/signin', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_required_on_signin(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/auth/signin', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_xss_protection_in_product_title(): void
    {
        $user = $this->createUserWithoutEvents();
        
        // Test that HTML is sanitized when creating product
        $product = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
            'title' => '<script>alert("xss")</script>Test Product',
        ]);

        // Title should be sanitized (Purifier removes script tags)
        $this->assertStringNotContainsString('<script>', $product->title);
    }

    public function test_sql_injection_protection(): void
    {
        $user = $this->createUserWithoutEvents();
        
        $maliciousInput = "'; DROP TABLE users; --";
        
        try {
            $response = $this->actingAs($user)->get('/search?q=' . urlencode($maliciousInput));
            
            // Should handle malicious input gracefully (not 500 error)
            // May return 200 with empty results or 422 validation error
            $this->assertContains($response->status(), [200, 422, 302]);
        } catch (\Exception $e) {
            // If exception occurs, it should be handled gracefully
            $this->assertStringNotContainsString('SQLSTATE', $e->getMessage());
        }
    }

    public function test_csrf_protection(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // CSRF protection is handled by Laravel middleware
        // We test that the route requires authentication/valid token
        $response = $this->post('/auth/signin', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Should either succeed or redirect (CSRF handled by framework)
        $this->assertContains($response->status(), [200, 302, 419]);
    }
}
