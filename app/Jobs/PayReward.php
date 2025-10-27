<?php

namespace App\Jobs;

use App\Enums\Order as EnumsOrder;
use App\Models\Order;
use App\Models\RevenueShare;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Facades\Log;

class PayReward implements ShouldQueue, ShouldBeUnique
{
  use Queueable;

  protected static int $platform_fee = 5;

  /**
   * Create a new job instance.
   */
  public function __construct(
    public Order|Subscription $model
  ) {}

  public $uniqueFor = 3600;

  public function uniqueId()
  {
    return $this->model->id;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {

    if ($this->model instanceof Order) {

      if ($this->model->status_id == EnumsOrder::REWARDING) {
        $revenue = RevenueShare::where('order_id', $this->model->id)->get();
        $system = User::find(0);

        DB::beginTransaction();
        try {
          foreach ($revenue as $rev) {
            $author = $rev->author;
            $referrer = $this->model->referrer_id ? User::find($this->model->referrer_id) : null;

            $author->increment('balance', $rev->author_amount);

            $system->increment('balance', $rev->service_amount);

            if ($referrer && $rev->referral_amount > 0) {
              $referrer->increment('balance', $rev->referral_amount);
            }
          }
        } catch (\Exception $e) {
          DB::rollBack();
          Log::critical('Error while paing rewards', [
            'rev' => $rev,
            'error' => $e,
          ]);
        }

        DB::commit();
      }

    }
  }
}
