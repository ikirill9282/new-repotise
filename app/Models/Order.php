<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use App\Services\Cart;
use Illuminate\Support\Collection;

class Order extends Model
{
    use HasStatus;

    protected static int $tax = 5;

    protected $guarded = ['id'];

    public function products()
    {
      return $this->belongsToMany(Product::class, OrderProducts::class, 'order_id', 'product_id', 'id', 'id')->withPivot(['count']);
    }

    public function promocode()
    {
      return $this->hasOne(Promocode::class, 'id', 'promocode');
    }

    public function getAmount(): int
    {
      return $this->products->reduce(function($c, $i) {
        return $c += $i->price * ($i->pivot->count ?? $i->pivot['count']);
      }, 0);
      // $result = 0;

      // if ($this->prepare) {
      //   $products = Product::whereIn('id', array_column($this->products, 'id'))->get();
      //   foreach ($this->products as $product) {
      //     $model = $products->where('id', $product['id'])->first();
      //     if ($product) {
      //       $result += ($model->price * $product['count']);
      //     }
      //   }
      // } else {
      //   return $this->products->reduce(function($c, $i) {
      //     return $c += $i->price * $i->pivot->count;
      //   }, 0);
      // }

      // return $result;
    }

    public function getCount(): int
    {
      return $this->prepare ? $this->products->count() : $this->products()->count();
    }

    public function getTax(): int
    {
      return static::calcPercent($this->getAmount(), static::$tax);
    }

    public function getDiscount(): int
    {
      if ($this->prepare && isset($this->promocode)) {
        return static::calcDiscount($this->getAmount(), $this->promocode);
      } else {
        return 0;
      }

      return ($this->promocode()->exists()) ? static::calcDiscount($this->getAmount(), $this->promocode) : 0;
    }

    public function getTotal(): int
    {
      return $this->getAmount() - $this->getDiscount() + $this->getTax();
    }

    public function getCosts()
    {
      return [
        'subtotal' => number_format($this->getAmount()),
        'discount' => number_format($this->getDiscount()),
        'tax' => number_format($this->getTax()),
        'total' => number_format($this->getTotal()),
      ];
    }

    public static function calcPercent(int $price, int $percent): int
    {
      return round(($price / 100) * $percent);
    }

    public function calcDiscount(int $price, Promocode $promo): int
    {
      return static::calcPercent($price, $promo->percent);
    }

    public static function prepare(Cart $cart): static
    {
      $order = new static();
      $order->prepare = true;
      $order->promocode = $cart->hasPromocode() ? Promocode::where('id', $cart->getCartPromocode())->first() : null;
      $order->products = static::prepareCartProducts($cart);

      return $order;
    }

    public static function prepareCartProducts(Cart $cart): Collection
    {
      $result = [];
      if ($cart->hasProducts()) {
        $products = $cart->getCartProducts();
        $result = array_map(function($item) use ($products) {
          $product = $products->where('id', $item['id'])->first();
          $product->pivot = ['count' => $item['count']];
          return $product;
        }, $cart->getProducts());
      }
      return collect($result);
    }
}
