<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class CartModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_modal_can_be_rendered(): void
    {
        $user = $this->createUserWithoutEvents();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Cart::class)
            ->assertSuccessful();
    }

    public function test_cart_modal_shows_products(): void
    {
        $user = $this->createUserWithoutEvents();
        $seller = $this->createUserWithoutEvents();
        
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'status_id' => \App\Enums\Status::ACTIVE,
            'published_at' => now(),
        ]);

        $cart = new Cart();
        $cart->addProduct($product->id, 1);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Modals\Cart::class)
            ->assertSee($product->title);
    }
}
