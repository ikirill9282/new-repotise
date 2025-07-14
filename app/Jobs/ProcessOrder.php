<?php

namespace App\Jobs;

use App\Enums\Order as EnumsOrder;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Order;
use Laravel\Cashier\Cashier;

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
        $paymentIntent = $this->order->getTransaction();
        $charge = Cashier::stripe()->charges->retrieve($paymentIntent->latest_charge);
        $transaction = Cashier::stripe()->balanceTransactions->retrieve($charge->balance_transaction);

        $this->order->update([
          'stripe_fee' => $transaction->fee / 100,
          'profit' => $transaction->net / 100,
          'status_id' => EnumsOrder::CALC_REWARD,
        ]);


      }
    }
}
