<?php

namespace App\Livewire\Profile\Tables;

use App\Models\RevenueShare;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sales extends Component
{
    public bool $showAll = false;

    public function loadAll(): void
    {
        $this->showAll = true;
    }

    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.sales', [
                'rows' => collect(),
                'summary' => [
                    'total_revenue' => 0.0,
                    'product_sales' => 0.0,
                    'recurring_revenue' => 0.0,
                ],
                'hasMore' => false,
            ]);
        }

        $baseQuery = RevenueShare::query()
            ->where('author_id', $userId);

        $rowsQuery = (clone $baseQuery)
            ->with(['order', 'product'])
            ->latest();

        if (!$this->showAll) {
            $rowsQuery->limit(10);
        }

        $rows = $rowsQuery->get();

        $summary = [
            'total_revenue' => (float) (clone $baseQuery)->sum('amount_paid'),
            'product_sales' => (float) (clone $baseQuery)->sum('author_amount'),
            'recurring_revenue' => (float) (clone $baseQuery)
                ->whereNotNull('subscription_id')
                ->sum('author_amount'),
        ];

        $hasMore = !$this->showAll
            && (clone $baseQuery)->count() > $rows->count();

        return view('livewire.profile.tables.sales', [
            'rows' => $rows,
            'summary' => $summary,
            'hasMore' => $hasMore,
        ]);
    }
}
