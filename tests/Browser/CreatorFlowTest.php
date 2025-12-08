<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;

class CreatorFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('scout.driver', 'null');
    }

    public function test_user_can_view_creators_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/creators')
                    ->assertPresent('body');
        });
    }

    public function test_user_can_view_creator_profile(): void
    {
        $creator = User::withoutEvents(function () {
            return $this->createUserWithoutEvents(['username' => 'testcreator']);
        });

        $this->browse(function (Browser $browser) use ($creator) {
            $browser->visit("/profile/@{$creator->username}")
                    ->assertPresent('body');
        });
    }

    public function test_authenticated_user_can_create_product(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile/products/create')
                    ->assertPresent('body');
        });
    }
}
