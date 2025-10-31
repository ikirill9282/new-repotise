<?php

namespace App\Livewire\Profile\Tables;

use App\Models\UserFunds;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Donation extends Component
{
    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.donation', [
                'rows' => collect(),
                'summary' => [
                    'donation_revenue' => 0.0,
                    'total_donations' => 0,
                ],
                'hasMore' => false,
            ]);
        }

        $baseQuery = UserFunds::query()
            ->where('user_id', $userId)
            ->where('group', 'donation')
            ->where('type', 'credit');

        $summary = [
            'donation_revenue' => (float) (clone $baseQuery)->sum('sum'),
            'total_donations' => (clone $baseQuery)->count(),
        ];

        $rows = (clone $baseQuery)
            ->with('related')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $hasMore = $summary['total_donations'] > $rows->count();

        return view('livewire.profile.tables.donation', [
            'rows' => $rows,
            'summary' => $summary,
            'hasMore' => $hasMore,
        ]);
    }
}
