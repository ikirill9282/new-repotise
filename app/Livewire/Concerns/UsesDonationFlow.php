<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use App\Models\Payments;
use App\Models\RevenueShare;
use App\Models\UserFunds;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\On;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;

trait UsesDonationFlow
{
    public ?User $seller = null;

    public ?User $donor = null;

    public array $guest = [
        'fullname' => '',
        'email' => '',
    ];

    public float $amount = 10.0;

    public bool $coverFees = false;

    public bool $anonymous = false;

    public string $message = '';

    public bool $monthlySupport = false;

    public array $paymentMethods = [];

    public ?string $selectedPaymentMethod = null;

    public ?string $setupIntentSecret = null;

    public float $processingPercent = 2.9;

    public float $processingFlat = 0.30;

    public float $platformPercent = 5.0;

    public string $currency = 'usd';

    public string $publishableKey;

    protected bool $awaitingAction = false;

    public bool $unavailable = false;

    public function mountDonation(?int $seller_id = null): void
    {
        $this->donor = Auth::user();

        $resolvedSellerId = $seller_id ?? (int) request()->query('seller_id', 0);

        if ($resolvedSellerId <= 0) {
            $this->unavailable = true;
            return;
        }

        $this->seller = User::with('options')->find($resolvedSellerId);

        if (!$this->seller) {
            $this->unavailable = true;
            return;
        }

        if ($this->donor && $this->seller->id === $this->donor->id) {
            abort(403, 'You cannot donate to yourself.');
        }

        $this->publishableKey = stripe_key() ?? '';

        if ($this->publishableKey === '') {
            Log::error('Stripe publishable key missing for donation.');
            abort(500, 'Payment configuration is missing.');
        }

        $this->currency = strtolower(config('cashier.currency', 'usd'));

        $this->platformPercent = $this->seller->options?->getFee() ?? 5.0;

        $this->paymentMethods = $this->resolvePaymentMethods();

        if (!empty($this->paymentMethods)) {
            $this->selectedPaymentMethod = $this->paymentMethods[0]['id'];
        } else {
            $this->selectedPaymentMethod = 'new';
        }

        $this->prepareSetupIntent();
    }

    protected function resolvePaymentMethods(): array
    {
        $methods = [];

        if (!$this->donor) {
            return $methods;
        }

        try {
            $collection = $this->donor?->paymentMethods();
        } catch (\Throwable $e) {
            Log::warning('Failed to load payment methods for donation', [
                'user_id' => $this->donor?->id,
                'error' => $e->getMessage(),
            ]);
            $collection = collect();
        }

        if ($collection && $collection->isNotEmpty()) {
            $methods = $collection->map(function ($method) {
                $brand = ucfirst($method->card->brand ?? 'Card');
                return [
                    'id' => $method->id,
                    'label' => $brand,
                    'last4' => $method->card->last4 ?? '0000',
                ];
            })->values()->all();
        }

        return $methods;
    }

    public function updatedAmount($value): void
    {
        $numeric = (float) preg_replace('/[^\d.]/', '', str_replace(',', '', (string) $value));
        $this->amount = round(max(0, $numeric), 2);
    }

    public function setAmount(float $value): void
    {
        $this->amount = round(max(0, $value), 2);
    }

    #[On('payment-method-added')]
    public function refreshPaymentMethods(?string $paymentMethodId = null): void
    {
        $this->paymentMethods = $this->resolvePaymentMethods();

        if ($paymentMethodId && $this->paymentMethodExists($paymentMethodId)) {
            $this->selectedPaymentMethod = $paymentMethodId;
        } elseif (!empty($this->paymentMethods) && !$this->paymentMethodExists((string) $this->selectedPaymentMethod)) {
            $this->selectedPaymentMethod = $this->paymentMethods[0]['id'];
        } elseif (empty($this->paymentMethods)) {
            $this->selectedPaymentMethod = 'new';
        }

        $this->prepareSetupIntent();
    }

    protected function paymentMethodExists(?string $paymentMethodId): bool
    {
        if (!$paymentMethodId) {
            return false;
        }

        return collect($this->paymentMethods)->pluck('id')->contains($paymentMethodId);
    }

    protected function rules(): array
    {
        $rules = [
            'amount' => ['required', 'numeric', 'min:1', 'max:10000'],
            'message' => ['nullable', 'string', 'max:500'],
            'coverFees' => ['boolean'],
            'anonymous' => ['boolean'],
            'monthlySupport' => ['boolean'],
        ];

        if (!$this->donor) {
            $rules['guest.fullname'] = ['required', 'string', 'max:255'];
            $rules['guest.email'] = ['required', 'email', 'max:255'];
        }

        return $rules;
    }

    public function getSummaryProperty(): array
    {
        $amount = round(max(0, $this->amount), 2);
        $chargeAmount = $this->calculateChargeAmount($amount, $this->coverFees);
        $stripeFeeEstimate = $this->estimateStripeFee($chargeAmount);
        $platformFeeEstimate = round($amount * ($this->platformPercent / 100), 2);

        $sellerReceives = $this->coverFees
            ? round(max(0, $amount - $platformFeeEstimate), 2)
            : round(max(0, $amount - $platformFeeEstimate - $stripeFeeEstimate), 2);

        return [
            'donation' => $amount,
            'total_charge' => $chargeAmount,
            'stripe_fee' => $stripeFeeEstimate,
            'platform_fee' => $platformFeeEstimate,
            'seller_receive' => $sellerReceives,
        ];
    }

    protected function calculateChargeAmount(float $amount, bool $coverFees): float
    {
        $amount = round(max(0, $amount), 2);

        if (!$coverFees) {
            return $amount;
        }

        return round($amount + $amount * ($this->processingPercent / 100) + $this->processingFlat, 2);
    }

    protected function estimateStripeFee(float $chargeAmount): float
    {
        if ($chargeAmount <= 0) {
            return 0.0;
        }

        return round(($chargeAmount * ($this->processingPercent / 100)) + $this->processingFlat, 2);
    }

    public function checkDonation(): array
    {
        $this->validate($this->rules());

        $selected = $this->selectedPaymentMethod;

        if ($this->shouldUseNewPaymentMethod($selected)) {
            if (!$this->setupIntentSecret) {
                $this->dispatch('toastError', ['message' => 'Adding a new payment method is temporarily unavailable. Please try again later.']);
                return ['error' => true];
            }

            return [
                'action' => 'create',
            ];
        }

        if (!$this->paymentMethodExists($selected)) {
            $this->dispatch('toastError', ['message' => 'Selected payment method is no longer available.']);
            return ['error' => true];
        }

        return [
            'action' => $selected,
        ];
    }

    #[On('makeDonation')]
    public function handleMakeDonation(array $payload): void
    {
        $needsFurtherAction = false;

        if ($this->unavailable || !$this->seller) {
            $this->dispatch('toastError', ['message' => 'Donations are currently unavailable for this creator.']);
            return;
        }

        try {
            $this->validate($this->rules());

            $paymentMethodId = $payload['pm_id'] ?? null;

            $donor = $this->ensureDonorAccount();

            if (!$donor) {
                $this->dispatch('toastError', ['message' => 'Please provide valid donor details.']);
                return;
            }

            if (!$this->paymentMethodExists($paymentMethodId)) {
                $this->attachPaymentMethodToCustomer($donor, $paymentMethodId);
                $this->refreshPaymentMethods($paymentMethodId);
            }

            $chargeAmount = $this->calculateChargeAmount($this->amount, $this->coverFees);

            if ($chargeAmount <= 0) {
                $this->dispatch('toastError', ['message' => 'Please enter a valid donation amount.']);
                return;
            }

            $metadata = [
                'type' => 'donation',
                'seller_id' => (string) $this->seller->id,
                'donor_id' => (string) $donor->id,
                'cover_fees' => $this->coverFees ? '1' : '0',
                'anonymous' => $this->anonymous ? '1' : '0',
                'monthly_support' => $this->monthlySupport ? '1' : '0',
                'donation_amount' => (string) $this->amount,
                'donor_email' => (string) $donor->email,
                'donor_name' => (string) ($donor->name ?? $this->guest['fullname'] ?? ''),
            ];

            if (!empty($this->message)) {
                $metadata['message'] = Str::limit(strip_tags($this->message), 500);
            }

            $intentData = [
                'amount' => (int) round($chargeAmount * 100),
                'currency' => $this->currency,
                'customer' => $donor->stripe_id,
                'payment_method' => $paymentMethodId,
                'confirmation_method' => 'automatic',
                'confirm' => true,
                'metadata' => $metadata,
                'description' => sprintf('Donation to %s', $this->seller->username ?? $this->seller->name ?? 'creator'),
            ];

            $intent = Cashier::stripe()->paymentIntents->create($intentData);

            if (in_array($intent->status, ['requires_action', 'requires_confirmation'])) {
                $needsFurtherAction = true;
                $this->dispatch('donation-requires-action', [
                    'componentId' => $this->getId(),
                    'clientSecret' => $intent->client_secret,
                    'paymentIntent' => $intent->id,
                ]);
                return;
            }

            if (!in_array($intent->status, [PaymentIntent::STATUS_SUCCEEDED, PaymentIntent::STATUS_PROCESSING])) {
                $this->dispatch('toastError', ['message' => 'Unable to process donation. Please try another payment method.']);
                return;
            }

            $this->finalizeDonation($intent);
        } catch (\Throwable $exception) {
            Log::error('Donation processing failed.', [
                'seller_id' => $this->seller->id ?? null,
                'donor_id' => $this->donor?->id,
                'error' => $exception->getMessage(),
            ]);
            $this->dispatch('toastError', ['message' => 'Unable to process donation. Please try again later.']);
        } finally {
            if (!$needsFurtherAction) {
                $this->dispatch('donation-processing-ended', [
                    'componentId' => $this->getId(),
                ]);
            }
        }
    }

    public function donationResult(string $result, string $paymentIntentId): void
    {
        if ($this->unavailable || !$this->seller) {
            $this->dispatch('toastError', ['message' => 'Donations are currently unavailable for this creator.']);
            return;
        }

        try {
            if ($result !== 'success') {
                $this->dispatch('toastError', ['message' => 'Donation authorization was cancelled or failed.']);
                return;
            }

            $intent = Cashier::stripe()->paymentIntents->retrieve($paymentIntentId);

            if (!in_array($intent->status, [PaymentIntent::STATUS_SUCCEEDED, PaymentIntent::STATUS_PROCESSING])) {
                $this->dispatch('toastError', ['message' => 'Unable to confirm donation payment.']);
                return;
            }

            $this->finalizeDonation($intent);
        } catch (\Throwable $exception) {
            Log::error('Donation confirmation failed.', [
                'seller_id' => $this->seller->id ?? null,
                'donor_id' => $this->donor?->id,
                'payment_intent' => $paymentIntentId,
                'error' => $exception->getMessage(),
            ]);
            $this->dispatch('toastError', ['message' => 'Unable to confirm donation payment. Please contact support if the issue persists.']);
        } finally {
            $this->dispatch('donation-processing-ended', [
                'componentId' => $this->getId(),
            ]);
        }
    }

    protected function finalizeDonation(PaymentIntent $paymentIntent): void
    {
        if ($this->unavailable || !$this->seller) {
            $this->dispatch('toastError', ['message' => 'Donations are currently unavailable for this creator.']);
            return;
        }

        if (Payments::where('stripe_id', $paymentIntent->id)->exists()) {
            $this->dispatch('closeModal');
            $this->dispatch('openModal', 'donate-accept', [
                'amount' => ($paymentIntent->amount_received ?? $paymentIntent->amount) / 100,
                'seller_name' => $this->seller->name ?? $this->seller->username ?? 'Creator',
            ]);
            return;
        }

        $metadata = $paymentIntent->metadata ?? [];
        $metadata = $metadata instanceof \Stripe\StripeObject ? $metadata->toArray() : (array) $metadata;

        $sellerId = (int) ($metadata['seller_id'] ?? $this->seller->id);
        $donorId = (int) ($metadata['donor_id'] ?? $this->donor?->id);

        if ($sellerId !== $this->seller->id || $donorId !== $this->donor?->id) {
            Log::warning('Donation metadata mismatch detected.', [
                'expected_seller' => $this->seller->id,
                'metadata_seller' => $sellerId,
                'expected_donor' => $this->donor?->id,
                'metadata_donor' => $donorId,
            ]);
            $this->dispatch('toastError', ['message' => 'Donation could not be verified.']);
            return;
        }

        $coverFees = Arr::get($metadata, 'cover_fees') === '1';
        $anonymous = Arr::get($metadata, 'anonymous') === '1';
        $donationAmount = (float) Arr::get($metadata, 'donation_amount', $this->amount);
        $message = Arr::get($metadata, 'message', $this->message);

        $gross = ($paymentIntent->amount_received ?? $paymentIntent->amount) / 100;

        $stripeFee = 0.0;
        try {
            if (!empty($paymentIntent->latest_charge)) {
                $charge = Cashier::stripe()->charges->retrieve(
                    $paymentIntent->latest_charge,
                    ['expand' => ['balance_transaction']]
                );
                $stripeFee = ($charge->balance_transaction->fee ?? 0) / 100;
            }
        } catch (ApiErrorException $exception) {
            Log::warning('Unable to retrieve Stripe charge details for donation.', [
                'payment_intent' => $paymentIntent->id,
                'error' => $exception->getMessage(),
            ]);
        }

        $platformFee = round($donationAmount * ($this->platformPercent / 100), 2);
        $authorAmount = $coverFees
            ? round(max(0, $donationAmount - $platformFee), 2)
            : round(max(0, $donationAmount - $platformFee - $stripeFee), 2);

        $sanitizedMessage = $message
            ? Str::limit(strip_tags($message), 500)
            : '';

        $userFund = UserFunds::create([
            'user_id' => $this->seller->id,
            'group' => 'donation',
            'type' => 'credit',
            'sum' => $authorAmount,
            'message' => $sanitizedMessage,
            'model' => $anonymous ? null : User::class,
            'model_id' => $anonymous ? null : $this->donor?->id,
        ]);

        Payments::create([
            'user_id' => $this->donor?->id ?? 0,
            'amount' => $gross,
            'stripe_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
            'paymentable_type' => UserFunds::class,
            'paymentable_id' => $userFund->id,
        ]);

        RevenueShare::create([
            'user_id' => $this->donor?->id ?? null,
            'author_id' => $this->seller->id,
            'amount_paid' => $gross,
            'stripe_fee' => $stripeFee,
            'net_amount' => $gross - $stripeFee,
            'author_amount' => $authorAmount,
            'service_amount' => $platformFee,
        ]);

        $this->dispatch('toastSuccess', ['message' => 'Thank you for supporting this creator!']);
        $this->dispatch('closeModal');
        $this->dispatch('openModal', 'donate-accept', [
            'amount' => $donationAmount,
            'charged_amount' => $gross,
            'seller_amount' => $authorAmount,
            'platform_fee' => $platformFee,
            'stripe_fee' => $stripeFee,
            'seller_name' => $this->seller->name ?? $this->seller->username ?? 'Creator',
            'cover_fees' => $coverFees,
            'anonymous' => $anonymous,
            'monthly_support' => Arr::get($metadata, 'monthly_support') === '1',
            'message' => $sanitizedMessage,
        ]);
        $this->resetDonationForm();
    }

    protected function resetDonationForm(): void
    {
        $this->amount = 10.0;
        $this->coverFees = false;
        $this->anonymous = false;
        $this->monthlySupport = false; 

