<?php

namespace Tests\Browser;

use App\Models\Product;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SearchTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_search_products(): void
    {
        $user = $this->createUserWithoutEvents();
        Product::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Product',
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                    ->type('q', 'Test')
                    ->press('Search')
                    ->assertSee('Test Product');
        });
    }

    public function test_search_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search')
                    ->assertSee('Search')
                    ->assertPresent('body');
        });
    }
}
