<?php

namespace App\Livewire\Profile;

use App\Models\RevenueShare;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Balances extends Component
{
    public string $period = 'today';

    protected function periodOptions(): array
    {
        return [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
        ];
    }

    protected function resolveDateRange(): array
    {
        $now = Carbon::now();

        return match ($this->period) {
            'yesterday' => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
            ],
            'week' => [
                $now->copy()->subDays(6)->startOfDay(),
                $now->copy()->endOfDay(),
            ],
            'month' => [
                $now->copy()->subDays(29)->startOfDay(),
                $now->copy()->endOfDay(),
            ],
            default => [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
            ],
        };
    }

    public function render()
    {
        $user = Auth::user();
        
        if (!$user) {
            return view('livewire.profile.balances', [
                'availableBalance' => 0.0,
                'pendingBalance' => 0.0,
                'totalBalance' => 0.0,
                'periodOptions' => $this->periodOptions(),
                'currentPeriod' => $this->period,
            ]);
        }

        [$from, $to] = $this->resolveDateRange();

        $baseQuery = RevenueShare::query()
            ->where('author_id', $user->id)
            ->whereNull('refunded_at')
            ->whereBetween('created_at', [$from, $to]);

        $availableBalance = (float) (clone $baseQuery)
            ->whereNotNull('paid_at')
            ->sum('author_amount');

        $pendingBalance = (float) (clone $baseQuery)
            ->whereNull('paid_at')
            ->sum('author_amount');

        $totalBalance = $availableBalance + $pendingBalance;

        return view('livewire.profile.balances', [
            'availableBalance' => $availableBalance,
            'pendingBalance' => $pendingBalance,
            'totalBalance' => $totalBalance,
            'periodOptions' => $this->periodOptions(),
            'currentPeriod' => $this->period,
        ]);
    }
}
