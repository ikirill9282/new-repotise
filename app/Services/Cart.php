<?php

namespace App\Services;

use App\Helpers\SessionExpire;
use Illuminate\Support\Collection;
use App\Models\Product;

class Cart
{
  protected array $cart = [];
  protected int $tax = 5;

  protected $template = [
    'products' => [],
    'promocode' => null,
    'payment_id' => null,
  ];

  public function __construct(
    protected string $name = 'cart',
  )
  {
    $this->loadCart();
  }

  public function flushCart()
  {
    $this->cart = $this->template;
    $this->updateCart($this->template);
  }

  public function addProduct(int $product_id, ?int $count = null)
  {
    if (!isset($this->cart['products'])) $this->cart['products'] = [];

    if ($this->inCart($product_id)) {
      foreach($this->cart['products'] as &$product) {
        if ($product['id'] == $product_id) {
          $product['count'] = $count ?? 1;
        }
      }
    } else {
      $this->cart['products'][] = [
        'id' => $product_id,
        'count' => $count,
      ];
    }

    $this->updateCart($this->cart);
  }

  public function removeProduct(int $product_id)
  {
    foreach ($this->cart['products'] as $key => $product) {
      if ($product['id'] == $product_id) {
        unset($this->cart['products'][$key]);
        break;
      }
    }

    $this->updateCart();
  }


  public function updateCart(): void
  {
    SessionExpire::saveCart($this->name, $this->cart);
    $this->loadCart();
  }

  public function loadCart()
  {
    $this->cart = SessionExpire::getCart($this->name) ?? [];
    if (empty($this->cart)) $this->cart = $this->template;
  }

  public function inCart(int $id)
  {
    return collect($this->cart['products'])->where('id', $id)->isNotEmpty();
  }

  public function getCartCount(): int
  {
    return collect($this->getProducts())->sum('count') ?? 0;

    // if ($this->hasProducts()) {
    //   return collect($this->getProducts())->sum('count');
    // }
    // return 0;
  }





















  
  public function getCart()
  {
    if (empty($this->cart)) {
      $this->loadCart();
    }

    return $this->cart;
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

  public function applyPromocode($promocode)
  {
    SessionExpire::addPromocode($this->name, $promocode->id);
    $this->loadCart();
  }

  public function getCartPromocode()
  {
    return $this->getCart()['promocode'] ?? null;
  }

  public function clearPromocode(): void
  {
    $this->cart['promocode'] = null;
    $this->updateCart($this->cart);
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

  public function calcDiscount($promo, ?int $price = null) {
    $int = is_null($price) ? $this->getCartAmount() : $price;
    return round(($int / 100) * $promo->percentage);
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

  public function paymentExists()
  {
    
  }

  public function setPaymentId(string $payment_id): void
  {
    $this->cart['payment_id'] = $payment_id;
    $this->updateCart($this->cart);
  }

  public function getPaymentId(): ?string
  {
    return $this->cart['payment_id'] ?? null;
  }
}