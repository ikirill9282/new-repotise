<?php

namespace App\Jobs;

use App\Livewire\UserNotify;
use App\Models\History;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use App\Mail\PayoutSetupActionNeeded;
use App\Mail\PayoutVerificationApproved;
use App\Mail\PayoutVerificationIssue;

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

          // VI.email.1 (TЗ): Action Needed: Set Up Your Payout Information
          $emailKey = 'email_stripe_verification_requires_input';
          if (!$this->user->notifications()->where('group', $emailKey)->exists()) {
            try {
              $actionUrl = $this->user->makeStripeVerificationUrl() ?? $this->user->makeProfileVerificationUrl();
              Mail::to($this->user->email)->send(new PayoutSetupActionNeeded(
                user: $this->user,
                requirements: [],
                dueDateLabel: null,
                actionUrl: $actionUrl,
              ));
              $this->user->notifications()->create([
                'type' => 'info',
                'group' => $emailKey,
                'message' => json_encode(['sent_at' => now()->toDateTimeString()]),
                'show' => 0,
                'closable' => 0,
              ]);
            } catch (\Throwable $e) {
              Log::warning('Failed to send payout setup action needed email', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
              ]);
            }
          }

          // VI.email.3 (TЗ): Important: Action Required for account verification issue
          if (!empty($verify_session->last_error)) {
            $issueKey = 'email_stripe_verification_issue';
            if (!$this->user->notifications()->where('group', $issueKey)->exists()) {
              try {
                $actionUrl = $this->user->makeStripeVerificationUrl() ?? $this->user->makeProfileVerificationUrl();
                Mail::to($this->user->email)->send(new PayoutVerificationIssue(
                  user: $this->user,
                  actionUrl: $actionUrl,
                ));
                $this->user->notifications()->create([
                  'type' => 'info',
                  'group' => $issueKey,
                  'message' => json_encode(['sent_at' => now()->toDateTimeString()]),
                  'show' => 0,
                  'closable' => 0,
                ]);
              } catch (\Throwable $e) {
                Log::warning('Failed to send payout verification issue email', [
                  'user_id' => $this->user->id,
                  'error' => $e->getMessage(),
                ]);
              }
            }
          }

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

          // VI.email.2 (TЗ): You're Verified! Your TrekGuider Account is Ready for Payouts!
          $emailKey = 'email_stripe_verification_verified';
          if (!$this->user->notifications()->where('group', $emailKey)->exists()) {
            try {
              Mail::to($this->user->email)->send(new PayoutVerificationApproved(
                user: $this->user,
                dashboardUrl: route('profile.dashboard'),
              ));
              $this->user->notifications()->create([
                'type' => 'info',
                'group' => $emailKey,
                'message' => json_encode(['sent_at' => now()->toDateTimeString()]),
                'show' => 0,
                'closable' => 0,
              ]);
            } catch (\Throwable $e) {
              Log::warning('Failed to send payout verification approved email', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
              ]);
            }
          }
          
          History::userVerified($this->user);
          $verify->delete();
          $this->user->update(['verified' => 1, 'stripe_verified_at' => now()]);
        }
      }
    }
}
