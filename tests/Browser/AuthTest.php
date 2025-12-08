<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

class AuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_sign_in(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                    ->clickLink('Sign In')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Sign In')
                    ->assertAuthenticated();
        });
    }

    public function test_user_can_sign_out(): void
    {
        $user = $this->createUserWithoutEvents();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->clickLink('Sign Out')
                    ->assertGuest();
        });
    }

    public function test_user_cannot_sign_in_with_invalid_credentials(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                    ->clickLink('Sign In')
                    ->type('email', $user->email)
                    ->type('password', 'wrong-password')
                    ->press('Sign In')
                    ->assertSee('credentials do not match');
        });
    }
}
