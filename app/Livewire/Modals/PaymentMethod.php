<?php

namespace App\Livewire\Modals;

use App\Livewire\Profile\Settings as SettingsComponent;
use App\Models\User;
use App\Traits\StoresStripePaymentMethods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Livewire\Component;

class PaymentMethod extends Component
{
    use StoresStripePaymentMethods;

    public string $clientSecret;
    public string $publishableKey;

    public function mount(): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $this->publishableKey = config('cashier.key') ?? env('STRIPE_KEY', '');

        if ($this->publishableKey === '') {
            Log::error('Stripe publishable key is not configured.');
            abort(500, 'Payment configuration incomplete.');
        }

        if (empty($user->stripe_id)) {
            $user->createOrGetStripeCustomer();
        }

        $intent = Cashier::stripe()->setupIntents->create([
            'customer' => $user->stripe_id,
            'payment_method_types' => ['card'],
            'usage' => 'off_session',
        ]);

        $this->clientSecret = $intent->client_secret;

        $this->dispatch('payment-method-open');
    }

    public function attachPaymentMethod(string $paymentMethodId): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        try {
            $paymentMethod = $this->storeStripePaymentMethod($user, $paymentMethodId);

            $user->updateDefaultPaymentMethod($paymentMethod->id);

            $this->dispatch('payment-method-added', $paymentMethod->id)->to(SettingsComponent::class);
            $this->dispatch('toastSuccess', ['message' => 'Payment method added successfully.']);
            $this->dispatch('payment-method-close');
            $this->dispatch('closeModal');
        } catch (\Throwable $exception) {
            Log::error('Failed to attach payment method from settings modal.', [
                'user_id' => $user->id ?? null,
                'payment_method' => $paymentMethodId,
                'error' => $exception->getMessage(),
            ]);

            $this->dispatch('payment-method-add-failed');
            $this->dispatch('toastError', ['message' => 'Unable to add payment method. Please try again.']);
        }
    }

    public function render()
    {
        return view('livewire.modals.payment-method', [
            'publishableKey' => $this->publishableKey,
            'clientSecret' => $this->clientSecret,
        ]);
    }
}
