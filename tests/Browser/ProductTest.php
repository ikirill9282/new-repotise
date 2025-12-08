<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Product;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_view_products_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/products')
                    ->assertSee('Products')
                    ->assertPresent('body');
        });
    }

    public function test_user_can_view_product_detail(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-product',
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit("/products/{$product->slug}")
                    ->assertSee($product->title)
                    ->assertPresent('body');
        });
    }

    public function test_authenticated_user_can_add_to_cart(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user, $product) {
            $browser->loginAs($user)
                    ->visit("/products/{$product->slug}")
                    ->press('Add to Cart')
                    ->assertSee('added to cart');
        });
    }
}
