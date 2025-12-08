<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_sign_in(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/auth/signin', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_sign_in_with_invalid_credentials(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/auth/signin', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_sign_out(): void
    {
        $user = $this->createUserWithoutEvents();
        
        $response = $this->actingAs($user)->post('/auth/signout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_user_account_gets_locked_after_multiple_failed_attempts(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
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

    public function test_locked_user_cannot_sign_in(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'login_locked_until' => now()->addMinutes(15),
            'failed_login_attempts' => 5,
        ]);

        $response = $this->post('/auth/signin', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
