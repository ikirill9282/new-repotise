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
use App\Traits\HasAuthor;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasStatus, HasAuthor;

    protected static int $tax = 5;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
      parent::booted();

      static::creating(function($model) {
        $ephemeralKey = \Stripe\EphemeralKey::create(
          ['customer' => $model->user->asStripeCustomer()->id],
          ['stripe_version' => '2022-11-15']
        );
        $transaction = Cashier::stripe()->paymentIntents->create([
          'amount' => ($model->cost * 100),
          'currency' => 'usd',
          'automatic_payment_methods' => ['enabled' => true],
          'customer' => $model->user->asStripeCustomer()->id,
          // 'payment_method_types' => ['card'],
          'metadata' => [
            'initiator' => ($model->user?->id ?? 0 == 0) ? 'system' : 'customer',
            'inititator_id' => $model->user?->id ?? 0,
            'ephermal' => $ephemeralKey->secret,
            'type' => 'order',
          ],
        ]);
        $model->payment_id = $transaction->id;
      });

      static::created(function($model) {
        Cashier::stripe()->paymentIntents->update($model->payment_id, ['metadata' => ['id' => $model->id]]);
      });

      static::deleting(function($model) {
        $model->cancelTransaction('Cancel by order delete.');
      });
      
    }

    public function free(): bool
    {
      return $this->discount && $this->cost == 0;
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
      return $this->prepare 
        ? $this->products->reduce(function($c, $i) {
          return $c += ($i->pivot['price'] * ($i->pivot['count'] ?? 1));
        }, 0)
        : $this->order_products->reduce(fn($c, $i) => $c += $i->getTotal(), 0);
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
        'total' => $item->pivot['price'] * ($item->pivot['count'] ?? 1),
        'total_without_discount' => $item->pivot['price'] * ($item->pivot['count'] ?? 1),
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

    public function cancelTransaction(string $message)
    {
        Cashier::stripe()->paymentIntents->update($this->payment_id, [
          'metadata' => [
            'message' => $message,
          ]
        ]);
        Cashier::stripe()->paymentIntents->cancel($this->payment_id);
    }

    public function getDiscount(): int
    {
      $sum = 0;
      if (!$this->discount()->exists()) return $sum;

      $discount = $this->discount;

      if ($discount->type == 'promocode') {
        if ($discount->target == 'cart') {
          $amount = $this->getAmount();

          if (!is_null($discount->percent)) {
            $sum = round(($amount / 100 * $discount->percent), 2);
            $sum = ($sum > $discount->max) ? $discount->max : $sum;
          }
        }
      }

      if ($this->discount->type == 'freeproduct') {
          $op = $this->findReferalFreeProduct();
          $max_discount = $op->product->author->id > 0 ? 25 : 50;

          return $max_discount > $op->price ? $op->price : $max_discount;
      }

      return floor($sum);
    }

    public function applyDiscount(Discount $discount)
    {
      $this->discount_id = $discount->id;
      $this->recalculateDiscount();
      $this->recalculateCosts();
      $this->saveThroughTransaction();
    }

    public function removeDiscount()
    {
      $this->discount_id = null;
      $this->recalculateDiscount();
      $this->recalculateCosts();
      $this->saveThroughTransaction();
    }

    public function recalculate()
    {
      $this->tax = $this->getTax();

      $this->recalculateDiscount();
      $this->recalculateStripeFee();
      $this->recalculateCosts();

      $this->saveThroughTransaction();
    }

    public function recalculateDiscount()
    {
      $this->discount_amount = $this->getDiscount();
      
      if (!is_null($this->discount_id) && $this->discount_amount) {
        $discount_max = $this->discount_amount;
        $discount_per_product = $discount_max / $this->order_products->count();

        if ($this->discount->type == 'promocode') {
          if ($this->discount->target == 'cart') {
            foreach ($this->order_products as $op) {
              $discount_product = $discount_per_product > $discount_max ? $discount_max : $discount_per_product;
              $discount_product = $discount_product > $op->price ? $op->price : $discount_product;
              
              $op->discount = $discount_product;
              $op->total = ($op->price * $op->count ?? 1) - $discount_product;
              $op->total_without_discount = ($op->price * $op->count ?? 1);
              $discount_max = $discount_max - $discount_product;

            }
          }
        }

        if ($this->discount->type == 'freeproduct') {
          $op = $this->findReferalFreeProduct();
          $op->discount = $this->getDiscount();
          $op->total = ($op->price * $op->count ?? 1) - $op->discount;
          $op->total_without_discount = ($op->price * $op->count ?? 1);
        }

      } else {
        $this->discount_amount = 0;
        foreach ($this->order_products as $op) {
          $op->discount = 0;
          $op->total = ($op->price * $op->count ?? 1);
          $op->total_without_discount = ($op->price * $op->count ?? 1);
        }
      }
    }

    public function recalculateStripeFee()
    {
      if (!is_null($this->stripe_fee)) {
        $this->base_reward = $this->cost - $this->stripe_fee;

        $fee_max = $this->stripe_fee;
        $fee_per_product = round($fee_max / $this->order_products->count(), 2);

        foreach ($this->order_products as $op) {
          if ($op->total > 0) {
            $fee_product = ($fee_per_product > $fee_max) ? $fee_max : $fee_per_product;
            if ($op == $this->order_products->last()) {
              $fee_product = $fee_max;
            }
            $op->payment_fee = $fee_product;
            $fee_max -= $fee_product;
          } else {
            $op->payment_fee = 0;
          }
        }
      }
    }


    public function recalculateCosts()
    {
      $this->cost_without_tax = $amount = $this->getAmount();
      $this->cost = $amount - $this->discount_amount + $this->tax;
      $this->cost_without_discount = $amount + $this->tax;
    }

    public function saveThroughTransaction()
    {
      DB::transaction(function() {
        $this->save();
        $this->order_products->map(fn($op) => $op->save());
      });
    }

    public function findReferalFreeProduct()
    {
      $res = [];
      foreach ($this->order_products as $op) {
        if ($op->price > 50) continue;
        if ($op->product->author->id > 0 && $op->price > 25) continue;
        
        $res[$op->product_id] = $op->price;
      }
      $cost = max($res);
      $product_id = array_search($cost, $res);
      return $this->order_products->where('product_id', $product_id)->first();
    }
}
