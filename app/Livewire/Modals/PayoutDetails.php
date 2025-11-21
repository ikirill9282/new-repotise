<?php

namespace App\Livewire\Modals;

use App\Models\Payout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PayoutDetails extends Component
{
    public ?int $payoutId = null;
    public ?Payout $payout = null;

    public function mount(?int $payout_id = null): void
    {
        $this->payoutId = $payout_id;
        $this->loadPayout();
    }

    public function updatedPayoutId($value): void
    {
        $this->loadPayout();
    }

    protected function loadPayout(): void
    {
        if (!$this->payoutId) {
            $this->payout = null;
            return;
        }

        $user = Auth::user();
        if (!$user) {
            $this->payout = null;
            return;
        }

        $this->payout = Payout::where('id', $this->payoutId)
            ->where('user_id', $user->id)
            ->with(['revenueShares.product', 'user'])
            ->first();
    }

    public function render()
    {
        return view('livewire.modals.payout-details');
    }
}

