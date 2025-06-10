<?php

namespace App\Jobs;

use App\Livewire\UserNotify;
use App\Models\History;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckStripeVerification implements ShouldQueue
{
    use Queueable;
    use IsMonitored;

    /**
     * Create a new job instance.
     */
    public function __construct(
      public User $user
    )
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      $verify = $this->user->getStripeVerify();

      if ($verify) {
        $verify_session = $this->user->getStripeVerifySession();
        UserNotification::clear($this->user->id, 'stripe_verification');

        if ($verify_session->status == 'requires_input') {
          UserNotification::create([
            'user_id' => $this->user->id,
            'type' => 'warning',
            'message' => "Please complete your verification on Stripe.",
            'group' => 'stripe_verification',
            'closable' => 0,
          ]);
          Log::info("User {$this->user->username} requires input for verification.", [
            'user' => $this->user,
            'verify' => $verify,
            'verify_session' => $verify_session,
          ]);
          History::userVerifyRequiresInput($this->user);
        }

        if ($verify_session->status == 'processing') {
          Log::info("User {$this->user->username} verification is processing.", [
            'user' => $this->user,
            'verify' => $verify,
            'verify_session' => $verify_session,
          ]);
          UserNotification::create([
            'user_id' => $this->user->id,
            'type' => 'info',
            'message' => "Your verification is in progress. Please wait.",
            'group' => 'stripe_verification',
            'closable' => 0,
          ]);
          History::userVerifyInProgress($this->user);
          self::dispatch($this->user)->delay(now()->addMinutes(1));
        }

        if ($verify_session->status == 'verified') {
          Log::info("User {$this->user->username} verification success.", [
            'user' => $this->user,
            'verify' => $verify,
            'verify_session' => $verify_session,
          ]);

          UserNotification::create([
            'user_id' => $this->user->id,
            'type' => 'success',
            'message' => "Verification success! Your account is now verified.",
          ]);
          History::userVerified($this->user);
          $verify->delete();
          $this->user->update(['verified' => 1, 'stripe_verified_at' => now()]);
        }
      }
    }
}
