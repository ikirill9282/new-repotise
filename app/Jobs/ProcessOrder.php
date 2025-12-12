<?php

namespace App\Jobs;

use App\Enums\Order as EnumsOrder;
use App\Helpers\Collapse;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Order;
use App\Models\Gift;
use App\Models\RevenueShare;
use App\Models\User;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;
use App\Jobs\DeliveryGift;

class ProcessOrder implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public $uniqueFor = 3600;

    public function uniqueId()
    {
      return $this->order->id;
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
      public Order $order
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      if ($this->order->status_id == EnumsOrder::PAID) {
        $paymentIntent = $this->order->getSuccessPayment()?->asStripePaymentIntent();
        
        if ($paymentIntent->status == PaymentIntent::STATUS_SUCCEEDED) {

          $amount = $paymentIntent->amount / 100; // cents!
          
          // Загружаем buyer для получения platform_fee
          $buyer = $this->order->buyer ?? $this->order->user;
          $platform_fee = $buyer->options->getFee();

          $charge = Cashier::stripe()->charges->retrieve(
            $paymentIntent->latest_charge,
            ['expand' => ['balance_transaction']]
          );

          $stripe_reward = $charge->balance_transaction->fee / 100; // cents!
          $base_reward = $amount - $stripe_reward;
          $stripe_fee_per_product = $this->distributeCommission(
            $stripe_reward, 
            $this->order->order_products->pluck('count', 'product_id')->toArray()
          );

          $rewards = [];

          foreach ($this->order->order_products as $order_product) {
            $amount_paid = $order_product->total;
            $service_amount = round($order_product->total / 100 * $platform_fee, 2);
            $stripe_fee = $stripe_fee_per_product[$order_product->product_id];
            $net_amount = $amount_paid - $stripe_fee;
            $referal_reward = 0;
            $author_amount = $amount_paid - $stripe_fee - $service_amount - $referal_reward;

            // Для подарков используем buyer_user_id, для обычных заказов - user_id
            $buyerId = $this->order->is_gift_order 
              ? ($this->order->buyer_user_id ?? $this->order->user_id)
              : $this->order->user_id;
            
            $revenue = [
              'user_id' => $buyerId,
              'author_id' => $order_product->product->user_id,
              'product_id' => $order_product->product_id,
              'order_id' => $this->order->id,
              'amount_paid' => $amount_paid,
              'stripe_fee' => $stripe_fee,
              'net_amount' => $net_amount,
              'author_amount' => $author_amount,
              'service_amount' => $service_amount,
            ];

            if ($referal_reward > 0) {
              $revenue['referrer_id'] = $order_product->product->referer->id;
              $revenue['referral_amount'] = $referal_reward;
            }

            $rewards[] = $revenue;

            $order_product->update([
              'payment_fee' => $stripe_fee,
              'seller_reward' => $author_amount,
              'referal_reward' => $referal_reward,
              'platform_reward' => $service_amount,
            ]);
          }

          $this->order->update([
            'stripe_fee' => $stripe_reward,
            'base_reward' => $base_reward,
            'seller_reward' => $this->order->order_products()->sum('seller_reward'),
            'referal_reward' => $this->order->order_products()->sum('referal_reward'),
            'platform_reward' => $this->order->order_products()->sum('platform_reward'),
            'status_id' => EnumsOrder::REWARDING,
          ]);

          foreach ($rewards as $item) {
            RevenueShare::create($item);
          }
        }

        // Обработка подарков
        if ($this->order->is_gift_order && $this->order->status_id == EnumsOrder::PAID) {
          $this->processGiftOrder();
          // Для подарков покупатель НЕ получает доступ к продукту
        } else {
          // Обычный заказ - создаем покупки для buyer_user_id
          ReferalFreeProduct::dispatch($this->order->buyer ?? $this->order->user);
        }

        PayReward::dispatch($this->order);
      }
    }


    protected function processGiftOrder(): void
    {
      // Создаем запись Gift
      $gift = Gift::create([
        'order_id' => $this->order->id,
        'buyer_user_id' => $this->order->buyer_user_id ?? $this->order->user_id,
        'recipient_email' => $this->order->recipient,
        'status' => Gift::STATUS_CREATED,
      ]);

      // Покупатель НЕ получает доступ к продукту при подарке
      // Письма будут отправлены через DeliveryGift job
      DeliveryGift::dispatch($this->order);
    }

    function distributeCommission(float $commission, array $items)
    {
      $totalParts = array_sum($items);
      $result = [];

      foreach ($items as $product_id => $count) {
          $share = round(($commission * $count) / $totalParts, 2);
          $result[$product_id] = $share;
      }

      $sum = array_sum($result);
      $remainder = round($commission - $sum, 2);
      $result[array_key_last($result)] += $remainder;

      return $result;
    }
}
