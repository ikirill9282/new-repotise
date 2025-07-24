<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use App\Services\Cart;
use Illuminate\Support\Collection;
use App\Enums\Order as EnumsOrder;
use Laravel\Cashier\Cashier;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessOrder;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasStatus;

    protected static int $tax = 5;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
      parent::booted();

      static::creating(function($model) {
        $transaction = Cashier::stripe()->paymentIntents->create([
          'amount' => ($model->cost * 100),
          'currency' => 'usd',
          'automatic_payment_methods' => ['enabled' => true],
          'metadata' => [
            'user_id' => Auth::user()?->id ?? 0,
          ],
        ]);
        $model->payment_id = $transaction->id;
      });

      static::created(function($model) {
        Cashier::stripe()->paymentIntents->update($model->payment_id, ['metadata' => ['order_id' => $model->id]]);
      });

      static::deleting(function($model) {
        Cashier::stripe()->paymentIntents->update($model->payment_id, [
          'metadata' => [
            'message' => 'Cancel by order delete.',
          ]
        ]);
        Cashier::stripe()->paymentIntents->cancel($model->payment_id);
      });
      
    }

    public function user()
    {
      return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function products()
    {
      return $this->belongsToMany(Product::class, OrderProducts::class, 'order_id', 'product_id', 'id', 'id')
        ->withPivot(['count', 'price', 'old_price', 'seller_reward']);
    }

    public function order_products()
    {
      return $this->hasMany(OrderProducts::class);
    }

    public function discount()
    {
      return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }

    public function complete()
    {
      $this->discount?->complete();
      $this->update([
        'status_id' => EnumsOrder::PAID,
      ]);
    }

    public function createPayment(int $amount): void
    {
      $total = $amount * 100;
      $transaction = Cashier::stripe()->paymentIntents->create([
        'amount' => $total,
        'currency' => 'usd',
        'automatic_payment_methods' => ['enabled' => true],
        'metadata' => [
          'user_id' => Auth::user()?->id ?? 0,
        ],
      ]);

      $this->setPaymentId($transaction->id);
    }

    public function getAmount(): int
    {
      return $this->products->reduce(function($c, $i) {
        return $c += $i->price * ($i->pivot->count ?? $i->pivot['count']);
      }, 0);
    }

    public function getCount(): int
    {
      return $this->prepare ? $this->products->count() : $this->products()->count();
    }

    public function getTax(): int
    {
      // $amount = $this->getAmount() - $this->getDiscount();
      // return static::calcPercent($amount, static::$tax);
      return 0;
    }

    public function getDiscount(): int
    { 
      if ($this->discount_amount > 0) return $this->discount_amount;
      
      return $this->discount()->exists() ? $this->discount->calcOrderDiscount($this) : 0;
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

    public static function preparing(Cart $cart): static
    {
      $order = new static();
      $order->prepare = true;
      $order->promocode = $cart->hasPromocode() ? $cart->getCartPromocode() : null;
      $order->products = static::preparingCartProducts($cart);
      $order->status_id = EnumsOrder::NEW;

      return $order;
    }

    public static function preparingCartProducts(Cart $cart): Collection
    {
      $result = [];
      if ($cart->hasProducts()) {
        $products = $cart->getCartProducts();
        $result = array_map(function($item) use ($products) {
          $product = $products->where('id', $item['id'])->first();
          $product->pivot = [
            'count' => $item['count'],
            'price' => $product->price,
            'old_price' => $product->old_price,
          ];
          return $product;
        }, $cart->getProducts());
      }
      return collect($result);
    }

    public function savePrepared(): static
    {
      if ($this->prepare) {
        $this->prepare = false;
        $order = Order::create($this->getPreparingAttributes());

        $order = $this->syncPreparedProducts($order);
        $order->refresh();
        
        return $order;
      }

      return $this;
    }

    public function mergePrepared(Order $order): static
    {
      $attributes = $this->getPreparingAttributes();
      unset($attributes['user_id']);

      $order->update($attributes);
      $order = $this->syncPreparedProducts($order);
      $order->refresh();

      return $order;
    }

    public function syncPreparedProducts(Order $order): static
    {
      $prepared = $this->products->map(fn($item) => [
        'product_id' => $item->id, 
        'count' => $item->pivot['count'], 
        'price' => $item->pivot['price'],
        'old_price' => $item->pivot['old_price'],
        'price_without_discount' => $item->pivot['price'],
      ])
        ->toArray();

      $order->products()->sync($prepared);
      return $order;
    }

    public function getPreparingAttributes(): array
    {
      return [
          'user_id' => $this->user_id,
          'payment_id' => $this->payment_id,
          'cost' => $this->getTotal(),
          'tax' => $this->getTax(),
          'cost_without_discount' => ($this->getAmount() + $this->getTax()),
          'cost_without_tax' => $this->getAmount(),
          'status_id' => EnumsOrder::NEW,
          'discount_id' => $this->discount_id,
          'recipient' => $this->recipent ?? null,
          'recipient_message' => $this->recipient_message ?? null,
      ];
    }

    public function getTransaction(): ?PaymentIntent
    {
      return $this->payment_id ? Cashier::stripe()->paymentIntents->retrieve($this->payment_id) : null;
    }
}
