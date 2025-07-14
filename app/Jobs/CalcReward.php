<?php

namespace App\Jobs;

use App\Enums\Order as EnumsOrder;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Order;

class CalcReward implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    protected int|float $platform_fee = 0.05;

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
      if ($this->order->status_id === EnumsOrder::CALC_REWARD) {
        $products = $this->order->products->groupBy('user_id');
        foreach ($products as $user_id => $products) {
          $user_reward = 0;
          foreach ($products as $product) {
            $cost = round($product->pivot->price * $product->pivot->count);
            $platform_fee = round($cost / 100 * $this->platform_fee, 2, PHP_ROUND_HALF_UP);
            $reward = $cost - $platform_fee;
            $product->pivot->update(['reward' => $reward]);
            $user_reward += $reward;
          }
        }
      }
    }
}
