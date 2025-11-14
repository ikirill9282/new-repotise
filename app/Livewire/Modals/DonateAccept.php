<?php

namespace App\Livewire\Modals;

use Livewire\Component;

class DonateAccept extends Component
{
    public float $amount = 0.0;

    public float $chargedAmount = 0.0;

    public float $sellerAmount = 0.0;

    public float $platformFee = 0.0;

    public float $stripeFee = 0.0;

    public string $sellerName = '';

    public string $message = '';

    public bool $coverFees = false;

    public bool $anonymous = false;

    public bool $monthlySupport = false;

    public function mount(
        ?float $amount = null,
        ?float $charged_amount = null,
        ?float $seller_amount = null,
        ?float $platform_fee = null,
        ?float $stripe_fee = null,
        ?string $seller_name = null,
        ?string $message = null,
        ?bool $cover_fees = null,
        ?bool $anonymous = null,
        ?bool $monthly_support = null,
    ): void {
        $this->amount = $amount !== null ? (float) $amount : 0.0;
        $this->chargedAmount = $charged_amount !== null ? (float) $charged_amount : $this->amount;
        $this->sellerAmount = $seller_amount !== null ? (float) $seller_amount : $this->amount;
        $this->platformFee = $platform_fee !== null ? (float) $platform_fee : 0.0;
        $this->stripeFee = $stripe_fee !== null ? (float) $stripe_fee : 0.0;
        $this->sellerName = $seller_name ?: 'Creator';
        $this->message = trim((string) ($message ?? ''));
        $this->coverFees = (bool) $cover_fees;
        $this->anonymous = (bool) $anonymous;
        $this->monthlySupport = (bool) $monthly_support;
    }

    public function render()
    {
        return view('livewire.modals.donate-accept');
    }
}
