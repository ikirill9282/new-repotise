<?php

namespace App\Jobs;

use App\Models\Discount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Order;
use App\Models\User;

class ReferalFreeProduct implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
      public User $user
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      $owner = $this->user->owner;
      if ($owner) {
        $referal_buyers_count = $owner->referal_buyers()->count();

        if (
          ($referal_buyers_count > 0)
          && ($referal_buyers_count % 10) == 0
          && ($referal_buyers_count / 10) !== $owner->referal_free_products()->count()
        )
        
        Discount::createForUsers([$owner->id], [
          'group' => 'referal',
          'type' => 'freeproduct',
          'visibility' => 'private',
          'target' => 'cart',
          'max' => 50,
          'uses' => 1,
        ]);
      }
    }
}
