<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Product;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;

class CheckoutTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('scout.driver', 'null');
    }

    public function test_user_can_access_checkout_page(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/payment/checkout')
                    ->assertPresent('body');
        });
    }

    public function test_checkout_shows_cart_items(): void
    {
        $user = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });

        $seller = User::withoutEvents(function () {
            return $this->createUserWithoutEvents();
        });

        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user, $product) {
            $browser->loginAs($user)
                    ->visit('/products/' . $product->slug)
                    ->assertPresent('body');
        });
    }
}
