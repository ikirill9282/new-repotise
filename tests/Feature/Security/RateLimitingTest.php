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
        ]);

        // Attempt login 5 times with wrong password
        for ($i = 0; $i < 5; $i++) {
            $this->post('/auth/signin', [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $user->refresh();
        $this->assertNotNull($user->login_locked_until);
        $this->assertGreaterThanOrEqual(5, $user->failed_login_attempts);
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
