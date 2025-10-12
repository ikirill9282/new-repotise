<?php

namespace App\Jobs;

use App\Enums\Order as EnumsOrder;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Order;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\DB;

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

      if ($this->order->status_id == EnumsOrder::NEW) {
        $this->order->complete();
      }

      if ($this->order->status_id == EnumsOrder::PAID) {
        
        if (!$this->order->free()) {

          $paymentIntent = $this->order->getTransaction();
          $charge = Cashier::stripe()->charges->retrieve($paymentIntent->latest_charge);
          $transaction = Cashier::stripe()->balanceTransactions->retrieve($charge->balance_transaction);
          
          $this->order->stripe_fee = $transaction->fee / 100;
          $this->order->base_reward = $transaction->net / 100;
        } else {
          $this->order->stripe_fee = 0;
          $this->order->base_reward = 0;
        }

        $this->order->status_id = EnumsOrder::REWARDING;
        if ($this->order->type == 'cart') {
          $this->order->recalculate();
        }

        PayReward::dispatch($this->order);
        ReferalFreeProduct::dispatch($this->order->user);

        if ($this->order->gift) {
          DeliveryGift::dispatch($this->order);
        }
      }
    }
}
