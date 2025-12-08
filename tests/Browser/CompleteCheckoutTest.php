<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Product;
use App\Services\Cart;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CompleteCheckoutTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_complete_checkout_flow(): void
    {
        $user = $this->createUserWithoutEvents([
            'email' => 'buyer@example.com',
            'email_verified_at' => now(),
        ]);

        $seller = $this->createUserWithoutEvents([
            'email' => 'seller@example.com',
        ]);

        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'price' => 100,
            'sale_price' => 10,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user, $product) {
            $browser->visit('/')
                ->loginAs($user)
                ->visit("/products/{$product->slug}")
                ->assertSee($product->title)
                ->click('@add-to-cart')
                ->pause(500)
                ->visit('/payment/checkout')
                ->assertSee('Checkout')
                ->assertSee($product->title);
        });
    }
}
