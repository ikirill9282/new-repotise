<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;

class CancelPaymentIntents implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
      protected array $paymentIntentIds = []
    )
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      foreach ($this->paymentIntentIds as $paymentIntentId) {
        try {
          $paymentIntent = Cashier::stripe()->paymentIntents->cancel(
            $paymentIntentId,
            []
          );
          Log::info("Payment intent {$paymentIntentId} cancelled successfully.", ['payment_intent' => $paymentIntent]);
          // TODO: ADD HISTORY LOGGING
        } catch (\Exception $e) {
          Log::error("Failed to cancel payment intent {$paymentIntentId}: " . $e->getMessage());
        }
      }
    }
}
