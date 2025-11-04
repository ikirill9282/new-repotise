<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Withdraw extends Component
{
    public float $available = 0.0;
    public float $amount = 0.0;
    public bool $coverFees = false;
    public string $speed = 'regular';
    public ?string $selectedPayoutMethod = null;

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

    public function mount(): void
    {
        $user = Auth::user();

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

    public function render()
    {
        return view('livewire.modals.withdraw', [
            'summary' => $this->buildSummary(),
            'speedOptions' => $this->speedOptions,
        ]);
    }
}
