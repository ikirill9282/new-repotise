<?php

namespace App\Jobs;

use App\Enums\Order as EnumsOrder;
use App\Helpers\Collapse;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Order;
use App\Models\RevenueShare;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;

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
          $platform_fee = $this->order->author->options->getFee();

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

            $revenue = [
              'user_id' => $this->order->user_id,
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

        ReferalFreeProduct::dispatch($this->order->user);
        
        if ($this->order->gift) {
          DeliveryGift::dispatch($this->order);
        }

        PayReward::dispatch($this->order);
      }
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
