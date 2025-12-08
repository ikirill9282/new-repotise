<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_view_public_profile(): void
    {
        $user = $this->createUserWithoutEvents(['username' => 'testuser']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit("/profile/@{$user->username}")
                    ->assertSee($user->username)
                    ->assertPresent('body');
        });
    }

    public function test_authenticated_user_can_access_profile(): void
    {
        $user = $this->createUserWithoutEvents();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertSee('Profile')
                    ->assertPresent('body');
        });
    }

    public function test_authenticated_user_can_view_orders(): void
    {
        $user = $this->createUserWithoutEvents();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile/orders')
                    ->assertSee('Orders')
                    ->assertPresent('body');
        });
    }
}
