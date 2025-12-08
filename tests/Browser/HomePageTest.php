<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Page;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class HomePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_home_page_loads(): void
    {
        Page::factory()->create(['slug' => 'home']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('TrekGuider')
                    ->assertPresent('body');
        });
    }

    public function test_user_can_navigate_to_products(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Products')
                    ->assertPathIs('/products');
        });
    }

    public function test_user_can_navigate_to_creators(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Creators')
                    ->assertPathIs('/creators');
        });
    }

    public function test_user_can_navigate_to_insights(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Insights')
                    ->assertPathIs('/insights');
        });
    }
}
