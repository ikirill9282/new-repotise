<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use App\Models\Category;
use App\Enums\Order as EnumsOrder;
use Illuminate\Support\Carbon;

class RevenueWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue by Category';

    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    protected function getChartHeight(): ?int
    {
        return 600;
    }

    protected function getData(): array
    {
        $period = $this->getPeriod();
        $startDate = $period['start'];
        $endDate = $period['end'];

        // Общий доход платформы (platform_reward)
        $totalRevenue = Order::query()
            ->where('status_id', '>=', EnumsOrder::PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('platform_reward') ?? 0;

        // Доход по категориям
        $revenueByCategory = Order::query()
            ->where('status_id', '>=', EnumsOrder::PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['order_products.product.categories'])
            ->get()
            ->flatMap(function($order) {
                if (!$order->order_products || $order->order_products->isEmpty()) {
                    return collect();
                }
                
                $productCount = $order->order_products->count();
                $platformReward = $order->platform_reward ?? 0;
                
                return $order->order_products->flatMap(function($orderProduct) use ($platformReward, $productCount) {
                    $product = $orderProduct->product;
                    if (!$product || !$product->categories) {
                        return collect();
                    }
                    
                    // Распределяем platform_reward пропорционально
                    $productRevenue = $productCount > 0 ? $platformReward / $productCount : 0;
                    
                    return $product->categories->map(function($category) use ($productRevenue) {
                        return [
                            'category_id' => $category->id,
                            'category_name' => $category->title,
                            'revenue' => $productRevenue,
                        ];
                    });
                });
            })
            ->groupBy('category_id')
            ->map(function($items) {
                return [
                    'name' => $items->first()['category_name'] ?? 'Uncategorized',
                    'revenue' => $items->sum('revenue'),
                ];
            })
            ->sortByDesc('revenue')
            ->take(10);

        $labels = $revenueByCategory->pluck('name')->toArray();
        $data = $revenueByCategory->pluck('revenue')->toArray();

        // Если нет данных по категориям, показываем общий доход
        if (empty($labels)) {
            $labels = ['Total Revenue'];
            $data = [$totalRevenue];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                ],
            ],
            'labels' => $labels,
            'total_revenue' => number_format($totalRevenue, 2),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getPeriod(): array
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);
        return [
            'start' => $startDate,
            'end' => $endDate,
        ];
    }
}

