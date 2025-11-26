<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use App\Models\Withdrawal;
use App\Models\UserFunds;
use App\Models\User;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class Withdraw extends Component
{
    public float $available = 0.0;
    public float $amount = 0.0;
    public bool $coverFees = false;
    public string $speed = 'regular';
    public ?string $selectedPayoutMethod = null;
    public ?string $twofaCode = null;

    public array $payoutMethods = [];

    public float $processingPercent = 2.9;
    public float $processingFlat = 0.30;

    protected array $speedOptions = [
        'regular' => [
            'label' => 'Regular Withdrawal',
            'fee_percent' => 0.25,
            'fee_fixed' => 0.25,
            'description' => 'Estimated arrival: 2-4 business days.',
        ],
        'express' => [
            'label' => 'Express Withdrawal',
            'fee_percent' => 1.0,
            'fee_fixed' => 0.0,
            'description' => 'Estimated arrival: ~30 minutes.',
        ],
    ];

    public function mount()
    {
        $user = Auth::user();
        
        // Check if user has full name filled (required for payouts)
        $fullName = $user?->options?->full_name ?? '';
        if (empty($fullName)) {
            // Redirect to settings if full name is not set
            session()->flash('error', 'Please complete your profile by adding your Full Name in Settings before requesting payouts.');
            return redirect()->route('profile.settings');
        }

        $this->available = (float) ($user?->funds()
            ->where('group', 'referal')
            ->sum('sum') ?? 0);

        $this->amount = $this->normalizeAmount(
            $this->available > 0 ? min(100, $this->available) : 0
        );

        $this->payoutMethods = $this->resolvePayoutMethods();

        if (empty($this->selectedPayoutMethod) && !empty($this->payoutMethods)) {
            $this->selectedPayoutMethod = $this->payoutMethods[0]['id'];
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

    public function updatedSpeed($value): void
    {
        if (!array_key_exists($value, $this->speedOptions)) {
            $this->speed = 'regular';
        }
    }

    protected function normalizeAmount(float $value): float
    {
        $value = max(0, $value);
        $max = $this->coverFees ? $this->maxAmountWithProcessingCovered() : $this->available;
        if ($max <= 0) {
            return 0.0;
        }

        return round(min($value, $max), 2);
    }

    protected function maxAmountWithProcessingCovered(): float
    {
        if ($this->available <= $this->processingFlat) {
            return 0.0;
        }

        $denominator = 1 + $this->processingPercent / 100;

        return round(max(0, ($this->available - $this->processingFlat) / $denominator), 2);
    }

    protected function resolvePayoutMethods(): array
    {
        $methods = [];

        if (!Auth::check()) {
            return $methods;
        }

        try {
            $collection = Auth::user()->paymentMethods();
        } catch (\Throwable $e) {
            Log::warning('Failed to load payment methods for withdrawal modal', ['error' => $e->getMessage()]);
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

    protected function speedFee(float $amount): float
    {
        $config = $this->speedOptions[$this->speed] ?? $this->speedOptions['regular'];
        $fee = $amount * ($config['fee_percent'] / 100);
        if (!empty($config['fee_fixed'])) {
            $fee += $config['fee_fixed'];
        }

        return round($fee, 2);
    }

    protected function buildSummary(): array
    {
        $amount = round(max(0, $this->amount), 2);
        $processingFee = $this->processingFee($amount);
        $speedFee = $this->speedFee($amount);

        $selectedFee = $speedFee + ($this->coverFees ? 0 : $processingFee);
        $receive = max(0, $amount - $speedFee - ($this->coverFees ? 0 : $processingFee));
        $debit = $this->coverFees ? min($this->available, $amount + $processingFee) : min($this->available, $amount);

        return [
            'amount' => $amount,
            'processing_fee' => $processingFee,
            'speed_fee' => $speedFee,
            'selected_fee' => round($selectedFee, 2),
            'receive' => round($receive, 2),
            'debit' => round($debit, 2),
        ];
    }

    public function submit()
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->dispatch('toastError', ['message' => 'You must be logged in to request a withdrawal.']);
            return;
        }

        // Validate 2FA if enabled
        if ($user->twofa) {
            $this->validateTwofa($user);
        }

        // Validate withdrawal data
        $this->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:' . $this->available],
            'selectedPayoutMethod' => ['required', 'string'],
            'speed' => ['required', 'in:regular,express'],
        ], [
            'amount.required' => 'Please enter the withdrawal amount.',
            'amount.min' => 'The withdrawal amount must be at least $1.00.',
            'amount.max' => 'The withdrawal amount cannot exceed your available balance.',
            'selectedPayoutMethod.required' => 'Please select a payout method.',
            'speed.required' => 'Please select a withdrawal speed.',
        ]);

        $summary = $this->buildSummary();

        // Check if user has sufficient balance
        if ($summary['debit'] > $this->available) {
            $this->addError('amount', 'Insufficient balance for this withdrawal.');
            return;
        }

        try {
            DB::transaction(function () use ($user, $summary) {
                // Create withdrawal record
                $withdrawal = Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $summary['amount'],
                    'fee' => $summary['speed_fee'],
                    'processing_fee' => $summary['processing_fee'],
                    'receive_amount' => $summary['receive'],
                    'currency' => 'USD',
                    'speed' => $this->speed,
                    'cover_fees' => $this->coverFees,
                    'payout_method_id' => $this->selectedPayoutMethod,
                    'status' => Withdrawal::STATUS_PENDING,
                ]);

                // Create debit record in user_funds
                UserFunds::create([
                    'user_id' => $user->id,
                    'group' => 'referal',
                    'type' => 'debit',
                    'sum' => $summary['debit'],
                    'message' => "Withdrawal request #{$withdrawal->id} - {$this->speed} withdrawal",
                    'model' => Withdrawal::class,
                    'model_id' => $withdrawal->id,
                ]);
            });

            $this->dispatch('closeModal');
            $this->dispatch('toastSuccess', ['message' => 'Withdrawal request submitted successfully. It will be processed shortly.']);
            
            // Refresh the page to update balance
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            Log::error('Failed to process withdrawal request', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('toastError', ['message' => 'Failed to process withdrawal request. Please try again later.']);
        }
    }

    protected function validateTwofa(User $user): void
    {
        $this->validate([
            'twofaCode' => ['required', 'digits:6'],
        ], [
            'twofaCode.required' => 'Please enter your two-factor authentication code.',
            'twofaCode.digits' => 'The 2FA code must be 6 digits.',
        ]);

        if (empty($user->google2fa_secret)) {
            throw ValidationException::withMessages([
                'twofaCode' => ['Two-factor authentication is not properly configured. Please contact support.'],
            ]);
        }

        try {
            $secret = Crypt::decryptString($user->google2fa_secret);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt 2FA secret during withdrawal', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'twofaCode' => ['Failed to verify 2FA code. Please try again.'],
            ]);
        }

        $code = preg_replace('/\s+/', '', $this->twofaCode ?? '');

        if (!Google2FA::verifyKey($secret, $code, 4)) {
            throw ValidationException::withMessages([
                'twofaCode' => ['Invalid two-factor authentication code. Please try again.'],
            ]);
        }
    }

    #[On('payment-method-added')]
    public function refreshPayoutMethods($paymentMethodId = null): void
    {
        $this->payoutMethods = $this->resolvePayoutMethods();
        
        // If a new payment method was added and we don't have a selected one, select the new one
        if ($paymentMethodId && empty($this->selectedPayoutMethod)) {
            $this->selectedPayoutMethod = $paymentMethodId;
        } elseif (empty($this->selectedPayoutMethod) && !empty($this->payoutMethods)) {
            // Select the first available method
            $this->selectedPayoutMethod = $this->payoutMethods[0]['id'];
        }
    }

    public function render()
    {
        $user = Auth::user();
        $requiresTwofa = $user && $user->twofa;

        return view('livewire.modals.withdraw', [
            'summary' => $this->buildSummary(),
            'speedOptions' => $this->speedOptions,
            'requiresTwofa' => $requiresTwofa,
        ]);
    }
}
