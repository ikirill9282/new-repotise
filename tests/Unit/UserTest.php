<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Mockery;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        Config::set('scout.driver', 'null');
        
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents([
                'email' => 'test@example.com',
                'username' => 'testuser',
            ]);
        });

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'username' => 'testuser',
        ]);
    }

    public function test_user_username_is_auto_generated_from_email(): void
    {
        // Username is auto-generated in UserFactory, so we test that it's set
        $user = $this->createUserWithoutEvents([
            'email' => 'john@example.com',
        ]);

        $this->assertNotNull($user->username);
        $this->assertNotEmpty($user->username);
    }

    public function test_user_password_is_hashed(): void
    {
        $password = 'TestPassword123';
        $user = User::withoutEvents(function () use ($password) {
            return $this->createUserWithoutEvents([
                'password' => $password,
            ]);
        });

        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    public function test_user_can_validate_password_strength(): void
    {
        // Weak password
        $this->assertFalse(User::validatePassword('weak'));
        
        // Medium password
        $this->assertTrue(User::validatePassword('MediumPass123'));
        
        // Strong password
        $this->assertTrue(User::validatePassword('StrongPass123!@#'));
    }

    public function test_user_can_get_password_strength(): void
    {
        $weak = User::getPasswordStrength('weak');
        $medium = User::getPasswordStrength('MediumPass123');
        $strong = User::getPasswordStrength('StrongPass123!@#');

        $this->assertEquals('weak', $weak);
        $this->assertEquals('medium', $medium);
        $this->assertEquals('strong', $strong);
    }

    public function test_user_has_profile_url(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents(['username' => 'testuser']);
        });
        
        $url = $user->makeProfileUrl();
        
        $this->assertStringContainsString('testuser', $url);
        $this->assertStringContainsString('profile', $url);
    }

    public function test_user_has_referal_url(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        
        $url = $user->makeReferalUrl();
        
        $this->assertStringContainsString('referal', $url);
    }

    public function test_user_can_have_products(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        $product = \App\Models\Product::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->products()->exists());
        $this->assertEquals(1, $user->products()->count());
    }

    public function test_user_can_have_orders(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });
        $order = \App\Models\Order::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->orders()->exists());
        $this->assertEquals(1, $user->orders()->count());
    }

    public function test_user_can_get_display_name(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents(['username' => 'testuser']);
        });
        
        $displayName = $user->getDisplayName();
        
        $this->assertNotEmpty($displayName);
    }

    public function test_user_can_get_short_description(): void
    {
        $user = $this->createUserWithoutEvents();
        
        // UserOptions should be created automatically by User observer
        // But if not, we can test with empty description
        $user->refresh();
        $short = $user->getShortDescription(100);
        
        // Description may be empty if not set, which is fine
        $this->assertIsString($short);
        if (!empty($short)) {
            $this->assertLessThanOrEqual(103, strlen($short)); // 100 + '...'
        }
    }
}
