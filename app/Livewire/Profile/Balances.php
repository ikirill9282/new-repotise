<?php

namespace App\Livewire\Profile;

use App\Models\RevenueShare;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Balances extends Component
{
    public function render()
    {
        $user = Auth::user();
        
        if (!$user) {
            return view('livewire.profile.balances', [
                'availableBalance' => 0.0,
                'pendingBalance' => 0.0,
                'totalBalance' => 0.0,
            ]);
        }

        // Available Balance - текущий баланс пользователя
        $availableBalance = (float) ($user->balance ?? 0.0);

        // Pending Balance - сумма всех author_amount из RevenueShare, где paid_at = null
        $pendingBalance = (float) RevenueShare::query()
            ->where('author_id', $user->id)
            ->whereNull('paid_at')
            ->whereNull('refunded_at')
            ->sum('author_amount');

        // Total Balance - сумма Available и Pending
        $totalBalance = $availableBalance + $pendingBalance;

        return view('livewire.profile.balances', [
            'availableBalance' => $availableBalance,
            'pendingBalance' => $pendingBalance,
            'totalBalance' => $totalBalance,
        ]);
    }
}
