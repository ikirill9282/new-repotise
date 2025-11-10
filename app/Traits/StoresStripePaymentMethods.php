<?php

namespace App\Traits;

use App\Models\User;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use Stripe\PaymentMethod as StripePaymentMethod;

trait StoresStripePaymentMethods
{
    protected function storeStripePaymentMethod(User $user, string $paymentMethodId): CashierPaymentMethod|StripePaymentMethod
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
}
