<?php

namespace App\Jobs;

use App\Models\Discount;
use App\Models\User;
use App\Models\UserReferal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ReferalPromocode implements ShouldQueue
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
      if ($this->user->owner()->exists()) {
        $owner = $this->user->owner;
        $ref = UserReferal::where(['owner_id' => $owner->id, 'referal_id' => $this->user->id])->first();
        if ($ref && !$ref->promocode) {
          DB::transaction(function() use ($owner, $ref) {
            Discount::createForUsers([$owner->id, $this->user->id], [
              'group' => 'referal',
              'visibility' => 'private',
              'type' => 'promocode',
              'target' => 'cart',
              'percent' => 15,
              'max' => 50,
            ]);
            $ref->update(['promocode' => 1]);
          });
        }
      }
    }
}
