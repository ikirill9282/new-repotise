<?php

namespace App\Livewire\Modals;

use App\Livewire\Profile\Settings as SettingsComponent;
use App\Models\User;
use App\Traits\StoresStripePaymentMethods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Livewire\Component;
use Livewire\Attributes\On;

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

        $this->publishableKey = stripe_key() ?? '';

        if ($this->publishableKey === '') {
            Log::error('Stripe publishable key is not configured.');
            abort(500, 'Payment configuration incomplete.');
        }

        if (empty($user->stripe_id)) {
            try {
                $user->createOrGetStripeCustomer();
            } catch (\Exception $e) {
                Log::error('Failed to create Stripe customer', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                abort(500, 'Unable to initialize payment. Please contact support.');
            }
        }

        try {
            $intent = Cashier::stripe()->setupIntents->create([
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'usage' => 'off_session',
            ]);

            $this->clientSecret = $intent->client_secret;
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe setup intent', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Unable to initialize payment. Please contact support.');
        }

        // Dispatch event after a small delay to ensure DOM is ready
        $this->dispatch('payment-method-open');
        
        // Also dispatch via JavaScript for immediate execution
        $this->dispatch('payment-method-open-js');
    }

    #[On('modal-opened')]
    public function refreshSetupIntent($data = null): void
    {
        // Handle different event formats
        $modal = null;
        if (is_array($data)) {
            $modal = $data['modal'] ?? $data[0]['modal'] ?? null;
        } elseif (is_string($data)) {
            $modal = $data;
        }
        
        // Only refresh if this is the payment-method modal
        if ($modal !== 'payment-method' && $modal !== null) {
            return;
        }

        $this->createNewSetupIntent();
    }

    public function getClientSecret(): string
    {
        $this->createNewSetupIntent();
        return $this->clientSecret;
    }

    protected function createNewSetupIntent(): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        if (empty($user->stripe_id)) {
            try {
                $user->createOrGetStripeCustomer();
            } catch (\Exception $e) {
                Log::error('Failed to create Stripe customer in createNewSetupIntent', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                return;
            }
        }

        try {
            // Create a new SetupIntent for each modal opening
            $intent = Cashier::stripe()->setupIntents->create([
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'usage' => 'off_session',
            ]);

            $this->clientSecret = $intent->client_secret;
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe setup intent in createNewSetupIntent', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return;
        }
        
        // Dispatch event with new client secret
        $this->dispatch('payment-method-client-secret-updated', ['clientSecret' => $this->clientSecret]);
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

            // Dispatch to SettingsComponent (for settings page)
            $this->dispatch('payment-method-added', $paymentMethod->id)->to(SettingsComponent::class);
            
            // Dispatch globally (for other components like Funds, Withdraw, etc.)
            $this->dispatch('payment-method-added', $paymentMethod->id);
            
            // VI.toast.8 (TЗ): Payout Method Added! ✅ (when coming from payout-method verification flow)
            if (session()->pull('payout_method_verified', false)) {
                $this->dispatch('toastSuccess', [
                    'heading' => 'Payout Method Added! ✅',
                    'message' => 'Your new payout method has been successfully added and verified.',
                ]);
            } else {
                $this->dispatch('toastSuccess', ['message' => 'Payment method added successfully.']);
            }
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
