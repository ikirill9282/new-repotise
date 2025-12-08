<?php

namespace Tests\Unit\Services;

use App\Services\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Session;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_can_be_instantiated(): void
    {
        $cart = new Cart();
        
        $this->assertInstanceOf(Cart::class, $cart);
    }

    public function test_cart_can_add_product(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create(['user_id' => $user->id]);
        
        $cart = new Cart();
        $cart->addProduct($product->id, 2);
        
        $this->assertTrue($cart->inCart($product->id));
        $this->assertEquals(2, $cart->getCartCount());
    }

    public function test_cart_can_remove_product(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create(['user_id' => $user->id]);
        
        $cart = new Cart();
        $cart->addProduct($product->id, 1);
        $cart->removeProduct($product->id);
        
        $this->assertFalse($cart->inCart($product->id));
    }

    public function test_cart_can_calculate_amount(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'price' => 100,
        ]);
        
        $cart = new Cart();
        $cart->addProduct($product->id, 2);
        
        $amount = $cart->getCartAmount();
        $this->assertEquals(200, $amount);
    }

    public function test_cart_can_calculate_tax(): void
    {
        $cart = new Cart();
        
        $tax = $cart->calcTax(100);
        $this->assertEquals(5, $tax);
    }

    public function test_cart_can_flush(): void
    {
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create(['user_id' => $user->id]);
        
        $cart = new Cart();
        $cart->addProduct($product->id, 1);
        $cart->flushCart();
        
        $this->assertFalse($cart->hasProducts());
    }

    public function test_cart_has_products_check(): void
    {
        $cart = new Cart();
        
        $this->assertFalse($cart->hasProducts());
        
        $user = $this->createUserWithoutEvents();
        $product = Product::factory()->create(['user_id' => $user->id]);
        $cart->addProduct($product->id, 1);
        
        $this->assertTrue($cart->hasProducts());
    }

    public function test_cart_can_get_products_ids(): void
    {
        $user = $this->createUserWithoutEvents();
        $product1 = Product::factory()->create(['user_id' => $user->id]);
        $product2 = Product::factory()->create(['user_id' => $user->id]);
        
        $cart = new Cart();
        $cart->addProduct($product1->id, 1);
        $cart->addProduct($product2->id, 1);
        
        $ids = $cart->getCartProductsIds();
        $this->assertContains($product1->id, $ids);
        $this->assertContains($product2->id, $ids);
    }
}
