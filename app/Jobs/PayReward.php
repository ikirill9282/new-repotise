<?php

namespace App\Jobs;

use App\Enums\Order as EnumsOrder;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use App\Models\UserFunds;
use Illuminate\Support\Carbon;
use App\Models\Discount;
use App\Models\OrderProducts;
use Exception;

class PayReward implements ShouldQueue, ShouldBeUnique
{
  use Queueable;

  protected static int $platform_fee = 5;

  /**
   * Create a new job instance.
   */
  public function __construct(
    public Order $order
  ) {}

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    if ($this->order->status_id !== EnumsOrder::REWARDING) {
      $max_fee = $this->order->stripe_fee;
      $processing_products = $this->order->order_products->where('price', '>', 0);
      $fee_per_product = round($this->order->stripe_fee / $processing_products->count(), 2);
      
      DB::beginTransaction();
      try {

        $sum_rewards = 0;
        $sum_referal_rewards = 0;
        $sum_platform_reward = 0;

        foreach ($this->order->order_products as $order_product) {
          if ($order_product->price == 0) {
            $order_product->update([
              'payment_fee' => 0,
              'seller_reward' => 0,
              'referal_reward' => 0,
              'platform_reward' => 0,
            ]);
            continue;
          }
          $author = $order_product->product->author;
          $platform_commission = $author->options->getCommissionPercent();

          $product_fee = ($fee_per_product > $max_fee) ? $max_fee : $fee_per_product;

          $reward = $order_product->price - $product_fee - ($order_product->price / 100 * $platform_commission);
          $platform_reward = $order_product->price - $product_fee - $reward;
          
          $owner = $author->owner;
          $referal_reward = 0;
          
          if ($owner) {
            $refreral_commission = 0;
            $register_date = Carbon::parse($author->created_at);

            if ($register_date->clone()->modify('+1 month')->gte(Carbon::today())) {
              $refreral_commission = 25;
            } elseif ($register_date->clone()->modify('+1 year')->gte(Carbon::today())) {
              $refreral_commission = 12.5;
            }

            if ($refreral_commission > 0) {
              $referal_reward = round($platform_reward / 100 * $refreral_commission, 2);
              $platform_reward = $platform_reward - $referal_reward;
            }
          }

          OrderProducts::where('id', $order_product->id)->update([
            'payment_fee' => $product_fee,
            'seller_reward' => $reward,
            'referal_reward' => $referal_reward,
            'platform_reward' => $platform_reward,
          ]);

          $author->funds()->create([
            'group' => 'reward',
            'type' => 'credit',
            'sum' => $reward,
            'message' => "Reward by selling product #[$order_product->product_id]",
          ]);

          $author->update(['balance' => DB::raw("`balance` + $reward")]);

          if ($owner && $referal_reward > 0) {
            $owner->funds()->create([
              'group' => 'referal',
              'type' => 'credit',
              'sum' => $referal_reward,
              'message' => "Reward by referal #[$author->id] selling product #[$order_product->id]",
            ]);
            $owner->update(['balance' => DB::raw("`balance` + $referal_reward")]);
          }

          $sum_rewards += $reward;
          $sum_referal_rewards += $referal_reward;
          $sum_platform_reward += $platform_reward;
          $max_fee = $max_fee - $product_fee;
        }
        
        $this->order->update([
          'status_id' => EnumsOrder::COMPLETE,
          'seller_reward' => $sum_rewards,
          'referal_reward' => $sum_referal_rewards,
          'platform_reward' => $sum_platform_reward,
        ]);

      } catch (\Exception $e) {
        DB::rollBack();
        dd($e);
        throw $e;
      } catch (\Error $e) {
        DB::rollBack();
        dd($e);
        throw $e;
      }
      DB::commit();
    }
  }

  public function getPlatformFee(int $price)
  {
    return $price / 100 * static::$platform_fee;
  }
}
