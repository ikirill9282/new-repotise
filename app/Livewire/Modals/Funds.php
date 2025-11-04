<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

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

    public function render()
    {
        return view('livewire.modals.funds', [
            'summary' => $this->buildSummary(),
        ]);
    }
}
