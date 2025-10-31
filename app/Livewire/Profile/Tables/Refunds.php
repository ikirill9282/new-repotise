<?php

namespace App\Livewire\Profile\Tables;

use App\Models\OrderProducts;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Refunds extends Component
{
    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.refunds', [
                'rows' => collect(),
                'summary' => [
                    'total_refunds' => 0,
                    'refund_rate' => 0.0,
                ],
                'hasMore' => false,
            ]);
        }

        $refundQuery = RefundRequest::query()
            ->where('seller_id', $userId);

        $totalRefunds = (clone $refundQuery)->count();

        $totalSales = OrderProducts::query()
            ->whereHas('product', fn ($query) => $query->where('user_id', $userId))
            ->count();

        $summary = [
            'total_refunds' => $totalRefunds,
            'refund_rate' => $totalSales > 0
                ? round($totalRefunds / $totalSales * 100, 2)
                : 0.0,
        ];

        $rows = (clone $refundQuery)
            ->with([
                'order',
                'orderProduct.product',
                'buyer',
            ])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $hasMore = $totalRefunds > $rows->count();

        return view('livewire.profile.tables.refunds', [
            'rows' => $rows,
            'summary' => $summary,
            'hasMore' => $hasMore,
        ]);
    }
}
