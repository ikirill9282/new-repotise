<?php

namespace App\Livewire\Profile\Tables;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\Subscriptions;
use Illuminate\Support\Carbon;

class Subs extends Component
{

    public string $user_id;

    protected $listeners = ['subs-refresh' => '$refresh'];

    public function mount(string $user_id)
    {
      $this->user_id = $user_id;
    }

    public function getUser(): ?User
    {
      return User::find(Crypt::decrypt($this->user_id));
    }

    public function moveCheckout(string $order_id)
    {
      $id = Crypt::decrypt($order_id);
      Session::put('checkout', $id);

      return redirect()->route('checkout');
    }

    public function completePayment(string $order_id)
    {
      Session::put('checkout', Crypt::decrypt($order_id));
      return redirect()->route('checkout.subscription');
    }

    public function render()
    {
      $user = $this->getUser();
      $subs = collect();

      if ($user) {
        $subs = $user->subscriptions()
          ->whereIn('stripe_status', ['active', 'trialing', 'incomplete'])
          ->get()
          ->map(function (Subscriptions $subscription) {
            $subscription->productModel = $this->resolveProduct($subscription);
            $subscription->periodLabel = $this->resolvePeriod($subscription);
            $subscription->latestPayment = $subscription->payments()->latest()->first();
            $subscription->latestPaymentIntent = $this->resolvePaymentIntent($subscription->latestPayment);
            $subscription->nextBillingDate = $this->resolveNextBillingDate($subscription);
            return $subscription;
          })
          ->filter(fn ($subscription) => !is_null($subscription->productModel));
      }

      return view('livewire.profile.tables.subs', [
        'user' => $user,
        'subs' => $subs,
      ]);
    }

    protected function resolveProduct(Subscriptions $subscription): ?Product
    {
      if (!str_starts_with($subscription->type, 'plan_')) {
        return null;
      }

      $parts = explode('_', $subscription->type);
      $productId = $parts[2] ?? null;

      return $productId ? Product::with(['preview', 'types', 'locations'])->find($productId) : null;
    }

    protected function resolvePeriod(Subscriptions $subscription): ?string
    {
      if (!str_starts_with($subscription->type, 'plan_')) {
        return null;
      }

      $parts = explode('_', $subscription->type);
      $period = $parts[1] ?? null;

      return $period ? ucfirst($period) : null;
    }

    protected function resolvePaymentIntent($payment): ?\Stripe\PaymentIntent
    {
      if (!$payment) {
        return null;
      }

      try {
        return $payment->asStripePaymentIntent(true);
      } catch (\Throwable $e) {
        return null;
      }
    }

    protected function resolveNextBillingDate(Subscriptions $subscription): ?Carbon
    {
      try {
        $stripeSub = $subscription->asStripeSubscription();
        if (!empty($stripeSub->current_period_end)) {
          return Carbon::createFromTimestamp($stripeSub->current_period_end);
        }
      } catch (\Throwable $e) {
        return null;
      }

      return null;
    }

    public function openSubscriptionModal(string $encryptedSubscriptionId): void
    {
      $this->dispatch(
        'openModal',
        modalName: 'subscription-product',
        args: [
          'subscription_id' => $encryptedSubscriptionId,
        ],
      );
    }

    public function openCancelModal(string $encryptedSubscriptionId): void
    {
      $this->dispatch(
        'openModal',
        modalName: 'cancelsub',
        args: [
          'subscription_id' => $encryptedSubscriptionId,
        ],
      );
    }
}
