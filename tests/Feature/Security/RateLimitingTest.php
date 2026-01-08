<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_locks_after_multiple_failed_login_attempts(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'failed_login_attempts' => 0,
            'login_locked_until' => null,
            'last_failed_login_at' => null,
        ]);

        // Attempt login 5 times with wrong password within 15 minutes
        // Need to ensure attempts are within the 15-minute window
        for ($i = 0; $i < 5; $i++) {
            $this->post('/auth/signin', [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
            
            // Refresh user after each attempt to get updated values
            $user->refresh();
        }

        $user->refresh();
        // Account should be locked after 5 failed attempts
        $this->assertNotNull($user->login_locked_until, 'Account should be locked after 5 failed attempts');
        $this->assertGreaterThanOrEqual(5, $user->failed_login_attempts, 'Failed login attempts should be at least 5');
        $this->assertGreaterThan(now(), $user->login_locked_until, 'Lock should be in the future');
    }

    public function test_locked_account_cannot_login(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'login_locked_until' => now()->addMinutes(15),
            'failed_login_attempts' => 5,
        ]);

        $response = $this->post('/auth/signin', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
