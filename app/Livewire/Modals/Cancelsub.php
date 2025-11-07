<?php

namespace App\Livewire\Modals;

use App\Models\Order;
use App\Models\Product;
use App\Models\Subscriptions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;

class Cancelsub extends Component
{

    public ?string $order_id = null;
    public ?string $subscription_id = null;

    public function mount(?string $order_id = null, ?string $subscription_id = null)
    {
      if ($order_id) {
        $this->order_id = $order_id;
      }

      if ($subscription_id) {
        $this->subscription_id = $subscription_id;
      }
    }

    public function getOrder(): ?Order
    {
      if (!$this->order_id) {
        return null;
      }

      return Order::find(Crypt::decryptString($this->order_id));
    }

    protected function getSubscription(): ?Subscriptions
    {
      if ($this->subscription_id) {
        try {
          $id = (int) Crypt::decryptString($this->subscription_id);
        } catch (\Throwable $e) {
          return null;
        }

        return Subscriptions::query()
          ->whereKey($id)
          ->where('user_id', Auth::id())
          ->first();
      }

      $order = $this->getOrder();
      if (!$order) {
        return null;
      }

      $subType = $order->getSubscriptionType();
      return $subType ? $order->user->subscription($subType) : null;
    }

    protected function resolveProduct(?Subscriptions $subscription, ?Order $order): ?Product
    {
      if ($order) {
        return $order->order_products->first()->product;
      }

      if (!$subscription || !str_starts_with($subscription->type, 'plan_')) {
        return null;
      }

      $parts = explode('_', $subscription->type);
      $productId = $parts[2] ?? null;

      return $productId ? Product::with(['preview'])->find($productId) : null;
    }

    public function cancelSubscription()
    {
      $subscription = $this->getSubscription();

      if (!$subscription) {
        $this->dispatch('toastError', ['message' => 'Subscription not found.']);
        return;
      }

      Cashier::stripe()->subscriptions->update(
        $subscription->stripe_id,
        ['cancel_at_period_end' => true],
      );

      $args = $this->subscription_id
        ? ['subscription_id' => $this->subscription_id]
        : ['order_id' => $this->order_id];

      $this->dispatch('openModal', 'cancelsub-accept', $args);
      $this->dispatch('subs-refresh');
    }

    public function render()
    {
        $order = $this->getOrder();
        $subscription = $this->getSubscription();

        if (!$subscription) {
          Log::error('Cant cancel. Undefined subscription.', [
            'order_id' => $this->order_id,
            'subscription_id' => $this->subscription_id,
          ]);
          $this->dispatch('toastError', ['message' => 'Something went wrong... Please contact with administration!']);
          return view('livewire.modals.cancelsub', [
            'product_name' => 'Subscription',
            'sub_end' => Carbon::now()->format('d.m.Y'),
          ]);
        }

        $product = $this->resolveProduct($subscription, $order);

        $stripeSub = $subscription->asStripeSubscription();
        $sub_end = Carbon::parse($stripeSub->current_period_end)->format('d.m.Y');

        return view('livewire.modals.cancelsub', [
          'product_name' => $product->title ?? 'Subscription',
          'sub_end' => $sub_end,
        ]);
    }
}
