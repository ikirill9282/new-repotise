<?php

namespace App\Livewire\Profile\Tables;

use App\Models\OrderProducts;
use App\Models\Product as ProductModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Product extends Component
{
    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.product', [
                'rows' => collect(),
                'summary' => [
                    'views' => 0,
                    'average_rating' => 0,
                ],
                'hasMore' => false,
            ]);
        }

        $productBaseQuery = ProductModel::query()
            ->where('user_id', $userId);

        $summary = [
            'views' => (int) (clone $productBaseQuery)->sum('views'),
            'average_rating' => round((float) ((clone $productBaseQuery)->where('rating', '>', 0)->avg('rating') ?? 0), 2),
        ];

        $orderStats = OrderProducts::query()
            ->select('order_products.product_id')
            ->selectRaw('SUM(order_products.count) as units_sold')
            ->selectRaw('SUM(order_products.total) as total_revenue')
            ->whereNotNull('order_products.seller_reward')
            ->groupBy('order_products.product_id');

        $rows = ProductModel::query()
            ->with('preview')
            ->where('products.user_id', $userId)
            ->leftJoinSub($orderStats, 'order_stats', function ($join) {
                $join->on('products.id', '=', 'order_stats.product_id');
            })
            ->select('products.*')
            ->selectRaw('COALESCE(order_stats.units_sold, 0) as units_sold')
            ->selectRaw('COALESCE(order_stats.total_revenue, 0) as total_revenue')
            ->orderByDesc(DB::raw('COALESCE(order_stats.total_revenue, 0)'))
            ->orderBy('products.title')
            ->limit(10)
            ->get();

        $productsCount = (clone $productBaseQuery)->count();

        $hasMore = $productsCount > $rows->count();

        return view('livewire.profile.tables.product', [
            'rows' => $rows,
            'summary' => $summary,
            'hasMore' => $hasMore,
        ]);
    }
}
