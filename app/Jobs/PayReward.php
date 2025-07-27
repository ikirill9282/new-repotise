<?php

namespace App\Jobs;

use App\Enums\Order as EnumsOrder;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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

  public $uniqueFor = 3600;

  public function uniqueId()
  {
    return $this->order->id;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    if ($this->order->status_id !== EnumsOrder::REWARDING) {

      foreach ($this->order->order_products as $op) {

        $author = $op->product->author;
        $owner = $author->owner;
        $platform_commission = $author->options->getFee();

        $op->platform_reward = round($op->total / 100 * $platform_commission, 2);
        
        if ($owner && $op->total > 0) {
          $refreral_commission = 0;
          $register_date = Carbon::parse($author->created_at);

          if ($register_date->clone()->modify('+1 month')->gte(Carbon::today())) {
            $refreral_commission = 25;
          } elseif ($register_date->clone()->modify('+1 year')->gte(Carbon::today())) {
            $refreral_commission = 12.5;
          }

          $op->referal_reward = round($op->platform_reward / 100 * $refreral_commission, 2);
          $op->platform_reward = $op->platform_reward - $op->referal_reward;
        } else {
          $op->referal_reward = 0;
        }

        $op->seller_reward = $op->total - $op->payment_fee - $op->referal_reward - $op->platform_reward;
      }
      
      $this->order->seller_reward = $this->order->order_products->sum('seller_reward');
      $this->order->referal_reward = $this->order->order_products->sum('referal_reward');
      $this->order->platform_reward = $this->order->order_products->sum('platform_reward');
      $this->order->status_id = EnumsOrder::COMPLETE;


      DB::transaction(function() {
        $this->order->save();
        $this->order->order_products->map(function($op) {
          $author = $op->product->author;
          $owner = $author->owner;

          $op->save();
          $author->funds()->create([
            'group' => 'reward',
            'type' => 'credit',
            'sum' => $op->seller_reward,
            'message' => "Reward by selling product #[$op->product_id]",
          ]);
          $author->update(['balance' => DB::raw("`balance` + $op->seller_reward")]);

          if ($owner && $op->referal_reward > 0) {
            $owner->funds()->create([
              'group' => 'referal',
              'type' => 'credit',
              'sum' => $op->referal_reward,
              'message' => "Reward by referal #[$author->id] selling product #[$op->product_id]",
              'model' => '\App\Models\Order',
              'model_id' => $op->order_id,
            ]);
            $owner->update(['balance' => DB::raw("`balance` + $op->referal_reward")]);
          }
        });
      });
    }
  }
}
