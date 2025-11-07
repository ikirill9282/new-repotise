<?php

namespace App\Livewire\Modals;

use App\Livewire\Profile\Settings as SettingsComponent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use Livewire\Component;
use Stripe\PaymentMethod as StripePaymentMethod;

class PaymentMethod extends Component
{
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
            $paymentMethod = $this->storePaymentMethod($user, $paymentMethodId);

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

    protected function storePaymentMethod(User $user, string $paymentMethodId): CashierPaymentMethod|StripePaymentMethod
    {
        $paymentMethod = Cashier::stripe()->paymentMethods->retrieve($paymentMethodId);
        $type = $paymentMethod->type;

        $existingMethods = Cashier::stripe()->paymentMethods->all([
            'customer' => $user->stripe_id,
            'type' => $type,
        ]);

        $matchedMethod = null;

        if ($type === 'card') {
            $fingerprint = $paymentMethod->card->fingerprint ?? null;

            foreach ($existingMethods->data as $method) {
                if (($method->card->fingerprint ?? null) === $fingerprint) {
                    $matchedMethod = $method;
                    break;
                }
            }
        } elseif ($type === 'sepa_debit') {
            $newBank = $paymentMethod->sepa_debit;

            foreach ($existingMethods->data as $method) {
                $existingBank = $method->sepa_debit;

                if (
                    ($existingBank->last4 ?? null) === ($newBank->last4 ?? null) &&
                    ($existingBank->bank_code ?? null) === ($newBank->bank_code ?? null)
                ) {
                    $matchedMethod = $method;
                    break;
                }
            }
        }

        if (!$matchedMethod) {
            $user->addPaymentMethod($paymentMethod->id);
            $matchedMethod = $paymentMethod;
        }

        return $matchedMethod;
    }

    public function render()
    {
        return view('livewire.modals.payment-method', [
            'publishableKey' => $this->publishableKey,
            'clientSecret' => $this->clientSecret,
        ]);
    }
}
