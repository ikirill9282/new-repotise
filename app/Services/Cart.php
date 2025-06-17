<?php

namespace App\Services;

use App\Helpers\SessionExpire;
use Illuminate\Support\Collection;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\Order;

class Cart
{
  protected array $cart = [];
  protected int $tax = 5;

  public function __construct(
    protected string $name = 'cart',
  )
  {
    $this->loadCart();
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
    return $this->hasProducts() ? collect($this->getProducts())->where('id', $id)->isNotEmpty() : false;
  }

  public function getCartCount(): int
  {
    if ($this->hasProducts()) {
      return collect($this->getProducts())->sum('count');
    }
    return 0;
  }

  public function getCartAmount(): int
  {
    if ($this->hasProducts()) {
      $result = 0;
      $models = $this->getCartProducts();
      $products = $this->getProducts();

      foreach ($products as $product) {
        $model = $models->where('id', $product['id'])->first();
        $result += ($model?->price * $product['count']);
      }

      return $result;
    }
    return 0;
  }

  public function getProducts(): array
  {
    return $this->hasProducts() ? $this->getCart()['products'] : [];
  }

  public function getCartProducts(): ?Collection
  {
    return $this->hasProducts() ? Product::whereIn('id', $this->getCartProductsIds())->get() : collect([]);
  }

  public function getCartProductsIds(): array
  {
    return ($this->hasProducts()) ? array_column($this->getProducts(), 'id') : [];
  }

  public function getCartPromocode()
  {
    return $this->getCart()['promocode'] ?? null;
  }

  public function removeFromCart(int $id): bool
  {
    if ($this->hasProducts()) {
      foreach ($this->getProducts() as $key => $product) {
        if ($product['id'] == $id) {
          unset($this->cart['products'][$key]);
          break;
        }
      }

      if (empty($this->cart['products'])) {
        unset($this->cart['promocode']);
      }

      $this->updateCart($this->cart);

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

  public function hasProducts(?array $cart = null)
  {
    if (is_null($cart)) $cart = $this->getCart();
    return !empty($cart) && isset($cart['products']) && !empty($cart['products']);
  }

  public function hasPromocode(?array $cart = null)
  {
    if (is_null($cart)) $cart = $this->getCart();
    return (isset($cart['promocode']) && !empty($cart['promocode']));
  }
}