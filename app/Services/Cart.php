<?php

namespace App\Services;

use App\Helpers\SessionExpire;
use Illuminate\Support\Collection;
use App\Models\Product;
use App\Models\Promocode;

class Cart
{
  protected array $cart = [];
  protected int $tax = 5;

  public function __construct(
    protected string $name,
  )
  {
    
  }

  public function flushCart()
  {
    $this->updateCart(['products' => []]);
  }

  public function getCart()
  {
    if (empty($this->cart)) {
      $this->loadCart();
    }

    return $this->cart;
  }

  public function updateCart(array $data): void
  {
    SessionExpire::saveCart($this->name, $data);
    $this->loadCart();
  }

  public function loadCart()
  {
    $this->cart = SessionExpire::getCart($this->name) ?? [];
  }

  public function inCart(int $id)
  {
    $cart = $this->getCart();
    return $this->hasProducts($cart) ? collect($cart['products'])->where('id', $id)->isNotEmpty() : false;
  }

  public function getCartCount(): int
  {
    $cart = $this->getCart();
    if ($this->hasProducts($cart)) {
      return collect($cart['products'])->sum('count');
    }
    return 0;
  }

  public function getCartAmount(): int
  {
    $cart = $this->getCart();
    if ($this->hasProducts($cart)) {
      $result = 0;
      $products = $this->getCartProducts();

      foreach ($cart['products'] as $product) {
        $model = $products->where('id', $product['id'])->first();
        if ($product) {
          $result += ($model->price * $product['count']);
        }
      }

      return $result;
    }
    return 0;
  }

  public function getCartProducts(): ?Collection
  {
    $cart = $this->getCart();
    return $this->hasProducts($cart) ? Product::whereIn('id', $this->getCartProductsIds())->get() : collect([]);
  }

  public function getCartProductsIds(): array
  {
    $cart = $this->getCart();
    return ($this->hasProducts($cart)) ? array_column($cart['products'], 'id') : [];
  }

  public function removeFromCart(int $id): bool
  {
    $cart = $this->getCart();
    if ($this->hasProducts($cart)) {
      foreach ($cart['products'] as $key => $product) {
        if ($product['id'] == $id) {
          unset($cart['products'][$key]);
          break;
        }
      }

      if (empty($cart['products'])) {
        unset($cart['promocode']);
      }

      $this->updateCart($cart);
      return true;
    }
    return false;
  }

  public function calcTax(?int $price = null)
  {
    $int = is_null($price) ? $this->getCartAmount() : $price;
    return round(($int / 100) * $this->tax);
  }

  public function calcDiscount(Promocode $promo, ?int $price = null) {
    $int = is_null($price) ? $this->getCartAmount() : $price;
    return round(($int / 100) * $promo->percentage);
  }

  public function applyPromocode(Promocode $promocode)
  {
    SessionExpire::addPromocode($this->name, $promocode->id);
    $this->loadCart();
  }

  protected function hasProducts(array $cart)
  {
    return !empty($cart) && isset($cart['products']);
  }
}