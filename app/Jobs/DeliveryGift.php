<?php

namespace App\Jobs;

use App\Mail\Gift;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class DeliveryGift implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    // public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     */
    public function __construct(
      public Order $order
    )
    {
    }

    public function uniqueId()
    {
      return $this->order->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      $user = User::where('email', $this->order->recipient)->first();
      if (!$user) {
        $credentials = [
          'email' => $this->order->recipient,
          'password' => User::makePassword(),
        ];
        $user = User::create($credentials);
      }

      Mail::to($user->email)
        ->send(new Gift(
          $user,
          $this->order,
          $credentials
        ));
    }
}
