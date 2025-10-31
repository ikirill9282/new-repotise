<?php
namespace App\Livewire\Profile\Tables;

use App\Enums\Order as OrderStatus;
use App\Models\OrderProducts;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductAnalytics extends Component
{
    public string $period = '30';

    public ?string $active = null;

    public function mount(?string $active = null, ?string $period = null): void
    {
        $this->active = $active;
        if (!empty($period)) {
            $this->period = $period;
        }
    }

    protected function getPeriodDays(): int
    {
        return match ($this->period) {
            '7' => 7,
            '60' => 60,
            default => 30,
        };
    }

    protected function dateFrom(): Carbon
    {
        return Carbon::now()->subDays($this->getPeriodDays());
    }

    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.product-analytics', [
                'rows' => collect(),
            ]);
        }

        $from = $this->dateFrom();

        $orderStats = OrderProducts::query()
            ->whereHas('product', fn ($q) => $q->where('user_id', $userId))
            ->whereHas('order', fn ($q) => $q
                ->where('status_id', '>=', OrderStatus::PAID)
                ->where('created_at', '>=', $from)
            )
            ->select('product_id',
                DB::raw('SUM(count) as units_sold'),
                DB::raw('SUM(total) as gross_revenue')
            );

        $orderStatsSub = DB::query()->fromSub($orderStats->groupBy('product_id'), 'order_stats');

        $products = Product::query()
            ->where('products.user_id', $userId)
            ->with(['preview'])
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->whereNull('parent_id')], 'rating')
            ->leftJoinSub($orderStatsSub, 'order_stats', fn ($join) => $join->on('products.id', '=', 'order_stats.product_id'))
            ->select('products.*')
            ->selectRaw('COALESCE(order_stats.units_sold, 0) as units_sold')
            ->selectRaw('COALESCE(order_stats.gross_revenue, 0) as gross_revenue')
            ->orderByDesc(DB::raw('COALESCE(order_stats.gross_revenue, 0)'))
            ->limit(20)
            ->get()
            ->map(function (Product $product) {
                $views = (int) ($product->views ?? 0);
                $unitsSold = (int) ($product->units_sold ?? 0);
                $grossRevenue = (float) ($product->gross_revenue ?? 0);

                $conversionRate = $views > 0 && $unitsSold > 0
                    ? round(($unitsSold / $views) * 100, 2)
                    : 0.0;

                return [
                    'product' => $product,
                    'views' => $views,
                    'units_sold' => $unitsSold,
                    'conversion_rate' => $conversionRate,
                    'average_rating' => round((float) ($product->avg_rating ?? 0), 2),
                    'gross_revenue' => round($grossRevenue, 2),
                ];
            });

        return view('livewire.profile.tables.product-analytics', [
            'rows' => $products,
        ]);
    }
}
