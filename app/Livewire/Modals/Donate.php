<?php

namespace App\Livewire\Modals;

use App\Mail\DonationReceived;
use App\Mail\DonationSent;
use App\Models\Payments;
use App\Models\RevenueShare;
use App\Models\User;
use App\Models\UserFunds;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\On;
use Livewire\Component;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;

class Donate extends Component
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

    public function mount(?int $seller_id = null): void
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
            Log::error('Stripe publishable key missing for donation modal.');
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
        if ($this->setupIntentSecret) {
            $this->dispatch('setup-intent-updated', $this->setupIntentSecret);
        }
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
            Log::warning('Failed to load payment methods for donation modal', [
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

    public function updatedSelectedPaymentMethod($value): void
    {
        if ($value === 'new') {
            $this->prepareSetupIntent();
            if ($this->setupIntentSecret) {
                $this->dispatch('setup-intent-updated', $this->setupIntentSecret);
            }
        }
        $this->dispatch('refreshPaymentMethods');
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
        $this->dispatch('refreshPaymentMethods');
        if ($this->setupIntentSecret) {
            $this->dispatch('setup-intent-updated', $this->setupIntentSecret);
        }
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

    public function checkDonation(): void
    {
        try {
            $this->validate($this->rules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toastError', ['message' => 'Please fill in all required fields correctly.']);
            $this->dispatch('donation-check-result', ['error' => true]);
            return;
        }

        $selected = $this->selectedPaymentMethod;

        if ($this->shouldUseNewPaymentMethod($selected)) {
            if (!$this->setupIntentSecret) {
                $this->prepareSetupIntent();
                if (!$this->setupIntentSecret) {
                    $this->dispatch('toastError', ['message' => 'Adding a new payment method is temporarily unavailable. Please try again later.']);
                    $this->dispatch('donation-check-result', ['error' => true]);
                    return;
                }
            }

            $this->dispatch('donation-check-result', [
                'action' => 'create',
            ]);
            return;
        }

        if (!$this->paymentMethodExists($selected)) {
            $this->dispatch('toastError', ['message' => 'Selected payment method is no longer available.']);
            $this->dispatch('donation-check-result', ['error' => true]);
            return;
        }

        $this->dispatch('donation-check-result', [
            'action' => $selected,
        ]);
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
                $this->showDonationError('Please provide valid donor details.', 'validation_error');
                return;
            }

            if (!$this->paymentMethodExists($paymentMethodId)) {
                $this->attachPaymentMethodToCustomer($donor, $paymentMethodId);
                $this->refreshPaymentMethods($paymentMethodId);
            }

            $chargeAmount = $this->calculateChargeAmount($this->amount, $this->coverFees);

            if ($chargeAmount <= 0) {
                $this->showDonationError('Please enter a valid donation amount.', 'validation_error');
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
                'return_url' => route('payment.success'),
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
                $errorMessage = $this->getStripeErrorMessage($intent);
                $this->showDonationError($errorMessage['message'], $errorMessage['code']);
                return;
            }

            $this->finalizeDonation($intent);
        } catch (CardException $exception) {
            $errorMessage = $this->getStripeErrorMessageFromException($exception);
            Log::error('Donation processing failed - Card error.', [
                'seller_id' => $this->seller->id ?? null,
                'donor_id' => $this->donor?->id,
                'error' => $exception->getMessage(),
                'code' => $exception->getStripeCode(),
                'decline_code' => $exception->getDeclineCode(),
            ]);
            $this->showDonationError($errorMessage['message'], $errorMessage['code']);
        } catch (ApiErrorException $exception) {
            $errorMessage = $this->getStripeErrorMessageFromException($exception);
            Log::error('Donation processing failed - Stripe API error.', [
                'seller_id' => $this->seller->id ?? null,
                'donor_id' => $this->donor?->id,
                'error' => $exception->getMessage(),
            ]);
            $this->showDonationError($errorMessage['message'], $errorMessage['code']);
        } catch (\Throwable $exception) {
            Log::error('Donation processing failed.', [
                'seller_id' => $this->seller->id ?? null,
                'donor_id' => $this->donor?->id,
                'error' => $exception->getMessage(),
            ]);
            $this->showDonationError('Unable to process donation. Please try again later.', 'processing_error');
        } finally {
            // Don't dispatch donation-processing-ended here - it will be dispatched
            // by showDonationError or finalizeDonation if needed
            if (!$needsFurtherAction && !$this->unavailable) {
                // Only dispatch if we're not showing an error or success modal
                // The error/success modals will handle their own cleanup
            }
        }
    }

    public function donationResult(string $result, string $paymentIntentId): void
    {
        if ($this->unavailable || !$this->seller) {
            $this->showDonationError('Donations are currently unavailable for this creator.', 'unavailable');
            return;
        }

        try {
            if ($result !== 'success') {
                $this->showDonationError('Donation authorization was cancelled or failed.', 'authorization_failed');
                return;
            }

            $intent = Cashier::stripe()->paymentIntents->retrieve($paymentIntentId);

            if (!in_array($intent->status, [PaymentIntent::STATUS_SUCCEEDED, PaymentIntent::STATUS_PROCESSING])) {
                $errorMessage = $this->getStripeErrorMessage($intent);
                $this->showDonationError($errorMessage['message'], $errorMessage['code']);
                return;
            }

            $this->finalizeDonation($intent);
        } catch (CardException $exception) {
            $errorMessage = $this->getStripeErrorMessageFromException($exception);
            Log::error('Donation confirmation failed - Card error.', [
                'seller_id' => $this->seller->id ?? null,
                'donor_id' => $this->donor?->id,
                'payment_intent' => $paymentIntentId,
                'error' => $exception->getMessage(),
                'code' => $exception->getStripeCode(),
                'decline_code' => $exception->getDeclineCode(),
            ]);
            $this->showDonationError($errorMessage['message'], $errorMessage['code']);
        } catch (ApiErrorException $exception) {
            $errorMessage = $this->getStripeErrorMessageFromException($exception);
            Log::error('Donation confirmation failed - Stripe API error.', [
                'seller_id' => $this->seller->id ?? null,
                'donor_id' => $this->donor?->id,
                'payment_intent' => $paymentIntentId,
                'error' => $exception->getMessage(),
            ]);
            $this->showDonationError($errorMessage['message'], $errorMessage['code']);
        } catch (\Throwable $exception) {
            Log::error('Donation confirmation failed.', [
                'seller_id' => $this->seller->id ?? null,
                'donor_id' => $this->donor?->id,
                'payment_intent' => $paymentIntentId,
                'error' => $exception->getMessage(),
            ]);
            $this->showDonationError('Unable to confirm donation payment. Please contact support if the issue persists.', 'confirmation_failed');
        }
        // Note: donation-processing-ended is dispatched by showDonationError or finalizeDonation
    }

    protected function finalizeDonation(PaymentIntent $paymentIntent): void
    {
        if ($this->unavailable || !$this->seller) {
            $this->showDonationError('Donations are currently unavailable for this creator.', 'unavailable');
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
            $this->showDonationError('Donation could not be verified. Please contact support if the issue persists.', 'verification_failed');
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

        // Send email notification to seller
        try {
            $donorName = $anonymous ? null : ($this->donor?->getName() ?? Arr::get($metadata, 'donor_name'));
            Mail::to($this->seller->email)->send(
                new DonationReceived(
                    seller: $this->seller,
                    amount: $donationAmount,
                    donorName: $donorName,
                    anonymous: $anonymous,
                    message: $sanitizedMessage,
                )
            );
        } catch (\Throwable $exception) {
            Log::warning('Failed to send donation received email.', [
                'seller_id' => $this->seller->id,
                'error' => $exception->getMessage(),
            ]);
        }

        // Send email confirmation to donor
        if ($this->donor) {
            try {
                $monthlySupport = Arr::get($metadata, 'monthly_support') === '1';
                Mail::to($this->donor->email)->send(
                    new DonationSent(
                        donor: $this->donor,
                        seller: $this->seller,
                        amount: $donationAmount,
                        monthlySupport: $monthlySupport,
                    )
                );
            } catch (\Throwable $exception) {
                Log::warning('Failed to send donation sent email.', [
                    'donor_id' => $this->donor->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $this->dispatch('toastSuccess', ['message' => 'Thank you for supporting this creator!']);
        $this->dispatch('donation-processing-ended', [
            'componentId' => $this->getId(),
        ]);
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
        $this->message = '';
        if (!$this->donor) {
            $this->guest = [
                'fullname' => '',
                'email' => '',
            ];
        }
        $this->paymentMethods = $this->resolvePaymentMethods();
        $this->selectedPaymentMethod = !empty($this->paymentMethods)
            ? $this->paymentMethods[0]['id']
            : 'new';
        $this->prepareSetupIntent();
    }

    protected function shouldUseNewPaymentMethod(?string $selected): bool
    {
        if (empty($this->paymentMethods)) {
            return true;
        }

        if (!$selected) {
            return true;
        }

        return $selected === 'new';
    }

    protected function prepareSetupIntent(): void
    {
        try {
            $params = [
                'payment_method_types' => ['card'],
            ];

            if ($this->donor) {
                if (empty($this->donor->stripe_id)) {
                    $this->donor->createOrGetStripeCustomer();
                }

                if (!empty($this->donor->stripe_id)) {
                    $params['customer'] = $this->donor->stripe_id;
                }
            }

            $intent = Cashier::stripe()->setupIntents->create($params);
            $this->setupIntentSecret = $intent->client_secret;
            $this->dispatch('setup-intent-updated', $this->setupIntentSecret);
        } catch (\Throwable $exception) {
            Log::warning('Unable to prepare setup intent for donation.', [
                'seller_id' => $this->seller->id ?? null,
                'donor_id' => $this->donor?->id,
                'error' => $exception->getMessage(),
            ]);
            $this->setupIntentSecret = null;
        }
    }

    protected function ensureDonorAccount(): ?User
    {
        if ($this->donor) {
            if (empty($this->donor->stripe_id)) {
                $this->donor->createOrGetStripeCustomer();
            }

            return $this->donor;
        }

        $fullname = trim($this->guest['fullname'] ?? '');
        $email = trim($this->guest['email'] ?? '');

        if ($fullname === '' || $email === '') {
            return null;
        }

        $password = User::makePassword();

        $donor = User::firstOrCreate(
            ['email' => $email],
            [
                'username' => $fullname,
                'password' => $password,
            ]
        );

        if ($donor->wasRecentlyCreated) {
            $donor->sendPassword($password);
            $donor->sendVerificationCode();
        }

        if (empty($donor->stripe_id)) {
            $donor->createOrGetStripeCustomer();
        }

        $this->donor = $donor;

        return $donor;
    }

    protected function attachPaymentMethodToCustomer(User $donor, ?string $paymentMethodId): void
    {
        if (!$paymentMethodId) {
            return;
        }

        try {
            $paymentMethod = Cashier::stripe()->paymentMethods->retrieve($paymentMethodId);

            $customerId = $donor->stripe_id;

            if (empty($customerId)) {
                $donor->createOrGetStripeCustomer();
                $customerId = $donor->stripe_id;
            }

            if (empty($customerId)) {
                Log::warning('Unable to attach payment method: donor lacks stripe customer.', [
                    'donor_id' => $donor->id,
                    'payment_method' => $paymentMethodId,
                ]);
                return;
            }

            if (empty($paymentMethod->customer)) {
                Cashier::stripe()->paymentMethods->attach($paymentMethodId, [
                    'customer' => $customerId,
                ]);
            } elseif ($paymentMethod->customer !== $customerId) {
                Log::warning('Payment method already attached to a different customer.', [
                    'donor_id' => $donor->id,
                    'payment_method' => $paymentMethodId,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Unable to attach payment method for donation.', [
                'donor_id' => $donor->id ?? null,
                'payment_method' => $paymentMethodId,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function showDonationError(string $message, ?string $errorCode = null): void
    {
        $this->dispatch('closeModal');
        $this->dispatch('openModal', 'donate-error', [
            'message' => $message,
            'errorCode' => $errorCode,
            'seller_id' => $this->seller->id ?? null,
        ]);
        $this->dispatch('donation-processing-ended', [
            'componentId' => $this->getId(),
        ]);
    }

    protected function getStripeErrorMessage(?PaymentIntent $intent): array
    {
        if (!$intent) {
            return [
                'message' => 'Unable to process donation. Please try another payment method.',
                'code' => 'processing_error',
            ];
        }

        $lastError = $intent->last_payment_error ?? null;

        if ($lastError) {
            return $this->mapStripeError(
                $lastError->code ?? null,
                $lastError->decline_code ?? null,
                $intent->currency ?? null
            );
        }

        return [
            'message' => 'Unable to process donation. Please try another payment method.',
            'code' => 'processing_error',
        ];
    }

    protected function getStripeErrorMessageFromException(\Throwable $exception): array
    {
        $code = null;
        $declineCode = null;

        if ($exception instanceof CardException) {
            $code = $exception->getStripeCode();
            $declineCode = $exception->getDeclineCode();
        } elseif ($exception instanceof ApiErrorException) {
            $code = $exception->getStripeCode();
        }

        return $this->mapStripeError($code, $declineCode);
    }

    protected function mapStripeError(?string $code, ?string $declineCode, ?string $currency = null): array
    {
        $errorMessages = [
            'card_declined' => 'Your card was declined. Please try a different card or contact your bank for details.',
            'insufficient_funds' => 'Insufficient funds. Your bank declined the charge — please use another card or payment method.',
            'expired_card' => 'This card has expired. Please use a different card or update the expiration date.',
            'incorrect_number' => 'Invalid card number. Please check the card number and try again.',
            'invalid_number' => 'The card number looks incorrect. Please re-enter it carefully or try another card.',
            'incorrect_cvc' => 'Incorrect security code (CVC). Re-enter the 3- or 4-digit code from your card.',
            'invalid_cvc' => 'Security code looks invalid. Check the CVC and try again.',
            'invalid_expiry_month' => 'Invalid expiration month. Please check the card\'s expiration date and try again.',
            'invalid_expiry_year' => 'Invalid expiration year. Please check the card\'s expiration date and try again.',
            'payment_intent_authentication_failure' => 'Authentication failed. Your bank didn\'t complete verification — try again or use a different payment method.',
            'setup_intent_authentication_failure' => 'Authentication failed. Your bank didn\'t complete verification — try again or use a different payment method.',
            'payment_method_not_available' => 'This payment method is temporarily unavailable. Please try again later or use another payment method.',
            'processing_error' => 'A processing error occurred. Please try again or use a different payment method.',
        ];

        $keys = array_filter([$code, $declineCode]);
        $keys[] = 'default';

        foreach ($keys as $key) {
            if ($key && isset($errorMessages[$key])) {
                return [
                    'message' => $errorMessages[$key],
                    'code' => $code ?? $declineCode ?? $key,
                ];
            }
        }

        return [
            'message' => 'Unable to process donation. Please try again later or use a different payment method.',
            'code' => $code ?? $declineCode ?? 'unknown_error',
        ];
    }

    public function render()
    {
        return view('livewire.modals.donate');
    }
}
