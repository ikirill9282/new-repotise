<?php

namespace App\Livewire\Modals;

use App\Models\Product;
use App\Models\Subscriptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class SubscriptionProduct extends Component
{
    public ?Subscriptions $subscription = null;

    public ?Product $product = null;

    public ?string $errorMessage = null;

    public function mount(?string $subscription_id = null): void
    {
        if (!$subscription_id) {
            $this->errorMessage = 'Subscription data is unavailable.';
            return;
        }

        try {
            $subscriptionId = (int) Crypt::decryptString($subscription_id);
        } catch (\Throwable $e) {
            $this->errorMessage = 'Invalid subscription reference.';
            return;
        }

        $subscription = Subscriptions::query()
            ->with('user')
            ->whereKey($subscriptionId)
            ->first();

        if (!$subscription || $subscription->user_id !== Auth::id()) {
            $this->errorMessage = 'You do not have access to this subscription.';
            return;
        }

        $product = $this->resolveProduct($subscription);

        if (!$product) {
            $this->errorMessage = 'No product is associated with this subscription.';
            return;
        }

        $this->subscription = $subscription;
        $this->product = $product;
    }

    protected function resolveProduct(Subscriptions $subscription): ?Product
    {
        $type = $subscription->type ?? '';

        if (!str_starts_with($type, 'plan_')) {
            return null;
        }

        $parts = explode('_', $type);
        $productId = $parts[2] ?? null;

        return $productId
            ? Product::with(['files', 'links', 'author'])->find($productId)
            : null;
    }

    public function render()
    {
        return view('livewire.modals.subscription-product', [
            'subscription' => $this->subscription,
            'product' => $this->product,
            'errorMessage' => $this->errorMessage,
        ]);
    }
}
