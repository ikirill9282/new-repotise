<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable Scout in tests
        Config::set('scout.driver', 'null');
        
        // Disable external services
        $this->disableExternalServices();
        
        // Run test seeder only if database is available (migrations run)
        // This will be handled by RefreshDatabase trait in tests that need it
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('statuses')) {
                $this->seed(\Database\Seeders\TestSeeder::class);
            }
        } catch (\Exception $e) {
            // Database not available, skip seeding
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Disable all external services for testing
     */
    protected function disableExternalServices(): void
    {
        // Disable Mailgun
        Config::set('services.mailgun.domain', 'test-domain');
        Config::set('services.mailgun.secret', 'test-secret');
        
        // Disable Google Analytics
        Config::set('services.ga4.property_id', null);
        Config::set('services.ga4.measurement_id', null);
        
        // Disable Socialite providers
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.facebook.client_id', 'test-client-id');
        Config::set('services.facebook.client_secret', 'test-client-secret');
        Config::set('services.x.client_id', 'test-client-id');
        Config::set('services.x.client_secret', 'test-client-secret');
        
        // Fake mail and notifications
        Mail::fake();
        Notification::fake();
    }

    /**
     * Helper method to create user without events (to avoid Stripe/Scout calls)
     */
    protected function createUserWithoutEvents(array $attributes = []): User
    {
        return User::withoutEvents(function () use ($attributes) {
            return User::factory()->create($attributes);
        });
    }

    /**
     * Mock Stripe API calls
     */
    protected function mockStripe(): void
    {
        Config::set('cashier.key', 'sk_test_mock');
        Config::set('cashier.secret', 'sk_test_mock');
    }

    /**
     * Mock Socialite providers
     */
    protected function mockSocialite(string $provider = 'google'): void
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn(str()->random(10))
            ->shouldReceive('getEmail')
            ->andReturn(fake()->email())
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')
            ->andReturn($abstractUser);

        Socialite::shouldReceive('driver')
            ->with($provider)
            ->andReturn($provider);
    }

    /**
     * Mock Google Analytics service
     */
    protected function mockGA4Service(): void
    {
        Config::set('services.ga4.property_id', 'properties/test');
        Config::set('services.ga4.measurement_id', 'G-TEST123');
    }
}
