<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;
use App\Models\UserFunds;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;

class Funds extends Component
{
    public float $amount = 50.0;
    public bool $coverFees = true;
    public ?string $selectedPaymentMethod = null;

    public array $paymentMethods = [];

    public float $processingPercent = 2.9;
    public float $processingFlat = 0.30;

    public function mount(): void
    {
        $this->amount = $this->normalizeAmount($this->amount);

        $this->paymentMethods = $this->resolvePaymentMethods();

        if (empty($this->selectedPaymentMethod) && !empty($this->paymentMethods)) {
            $this->selectedPaymentMethod = $this->paymentMethods[0]['id'];
        }
    }

    public function updatedAmount($value): void
    {
        $numeric = (float) preg_replace('/[^\d.]/', '', str_replace(',', '', (string) $value));
        $this->amount = $this->normalizeAmount($numeric);
    }

    public function updatedCoverFees(): void
    {
        $this->amount = $this->normalizeAmount($this->amount);
    }

    protected function normalizeAmount(float $value): float
    {
        return round(max(0, $value), 2);
    }

    protected function resolvePaymentMethods(): array
    {
        $methods = [];

        if (!Auth::check()) {
            return $methods;
        }

        try {
            $collection = Auth::user()->paymentMethods();
        } catch (\Throwable $e) {
            Log::warning('Failed to load payment methods for funds modal', ['error' => $e->getMessage()]);
            $collection = collect();
        }

        if ($collection instanceof Collection && $collection->isNotEmpty()) {
            $methods = $collection->map(function ($method) {
                $brand = $method->card->brand ?? 'Card';
                return [
                    'id' => $method->id,
                    'label' => ucfirst($brand),
                    'last4' => $method->card->last4 ?? '0000',
                ];
            })->values()->all();
        }

        return $methods;
    }

    protected function processingFee(float $amount): float
    {
        if ($amount <= 0) {
            return 0.0;
        }

        return round(($amount * ($this->processingPercent / 100)) + $this->processingFlat, 2);
    }

    protected function buildSummary(): array
    {
        $amount = $this->normalizeAmount($this->amount);
        $processingFee = $this->processingFee($amount);

        $credited = $this->coverFees
            ? $amount
            : max(0, $amount - $processingFee);

        $totalCharge = $this->coverFees
            ? $amount + $processingFee
            : $amount;

        return [
            'amount' => $amount,
            'processing_fee' => $processingFee,
            'credited' => round($credited, 2),
            'total_charge' => round($totalCharge, 2),
        ];
    }

    public function submit()
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->dispatch('toastError', ['message' => 'You must be logged in to add funds.']);
            return;
        }

        // Validate data
        $this->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'selectedPaymentMethod' => ['required', 'string'],
        ], [
            'amount.required' => 'Please enter the amount to add.',
            'amount.min' => 'The amount must be at least $1.00.',
            'selectedPaymentMethod.required' => 'Please select a payment method.',
        ]);

        $summary = $this->buildSummary();

        // Ensure user has Stripe customer
        if (empty($user->stripe_id)) {
            $user->createOrGetStripeCustomer();
        }

        try {
            DB::beginTransaction();

            // Create PaymentIntent
            $paymentIntent = Cashier::stripe()->paymentIntents->create([
                'customer' => $user->stripe_id,
                'amount' => (int) ($summary['total_charge'] * 100), // Convert to cents
                'currency' => 'usd',
                'payment_method' => $this->selectedPaymentMethod,
                'confirmation_method' => 'automatic',
                'confirm' => true,
                'return_url' => route('payment.success'),
                'metadata' => [
                    'type' => 'funds_topup',
                    'user_id' => $user->id,
                    'credited_amount' => $summary['credited'],
                ],
            ]);

            // Check if payment requires action (3D Secure, etc.)
            if ($paymentIntent->status === 'requires_action') {
                DB::rollBack();
                $this->dispatch('toastError', [
                    'message' => 'Your bank requires additional authentication. Please complete the verification and try again, or use a different payment method.'
                ]);
                return;
            }

            // Check if payment succeeded
            if ($paymentIntent->status !== 'succeeded') {
                DB::rollBack();
                $this->dispatch('toastError', ['message' => 'Payment failed. Please try again.']);
                return;
            }

            // Create credit record in user_funds
            UserFunds::create([
                'user_id' => $user->id,
                'group' => 'referal',
                'type' => 'credit',
                'sum' => $summary['credited'],
                'message' => "Funds top-up via payment method - Payment Intent: {$paymentIntent->id}",
                'model' => null,
                'model_id' => null,
            ]);

            DB::commit();

            $this->dispatch('closeModal');
            $this->dispatch('toastSuccess', ['message' => 'Funds added successfully! Your balance has been updated.']);
            
            // Refresh the page to update balance
            $this->dispatch('$refresh');

        } catch (CardException $e) {
            DB::rollBack();
            
            $errorCode = $e->getStripeCode();
            $declineCode = $e->getDeclineCode();
            
            Log::warning('Stripe card error during funds top-up', [
                'user_id' => $user->id,
                'error_code' => $errorCode,
                'decline_code' => $declineCode,
                'message' => $e->getMessage(),
            ]);

            $errorMessage = 'Payment failed. ';
            if ($declineCode) {
                $errorMessage .= ucfirst(str_replace('_', ' ', $declineCode)) . '.';
            } else {
                $errorMessage .= 'Please check your card details and try again.';
            }

            $this->dispatch('toastError', ['message' => $errorMessage]);

        } catch (RateLimitException | InvalidRequestException | AuthenticationException | ApiConnectionException | ApiErrorException $e) {
            DB::rollBack();
            
            Log::error('Stripe API error during funds top-up', [
                'user_id' => $user->id,
                'error_type' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            $this->dispatch('toastError', ['message' => 'Payment processing error. Please try again later.']);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::critical('Error during funds top-up', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('toastError', ['message' => 'Failed to process payment. Please try again later.']);
        }
    }

    #[On('payment-method-added')]
    public function refreshPaymentMethods($paymentMethodId = null): void
    {
        $this->paymentMethods = $this->resolvePaymentMethods();
        
        // If a new payment method was added and we don't have a selected one, select the new one
        if ($paymentMethodId && empty($this->selectedPaymentMethod)) {
            $this->selectedPaymentMethod = $paymentMethodId;
        } elseif (empty($this->selectedPaymentMethod) && !empty($this->paymentMethods)) {
            // Select the first available method
            $this->selectedPaymentMethod = $this->paymentMethods[0]['id'];
        }
    }

    public function render()
    {
        return view('livewire.modals.funds', [
            'summary' => $this->buildSummary(),
        ]);
    }
}
