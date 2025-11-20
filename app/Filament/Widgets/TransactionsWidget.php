<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use App\Enums\Order as EnumsOrder;
use Illuminate\Support\Carbon;

class TransactionsWidget extends ChartWidget
{
    protected static ?string $heading = 'Transactions';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getChartHeight(): ?int
    {
        return 180;
    }

    protected function getData(): array
    {
        $period = $this->getPeriod();
        $startDate = $period['start'];
        $endDate = $period['end'];

        $orders = Order::query()
            ->where('status_id', '>=', EnumsOrder::PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalTransactions = $orders->count();
        $totalRevenue = $orders->sum('cost');
        $averageOrder = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Группировка по дням для графика
        $dailyData = $orders->groupBy(function($order) {
            return Carbon::parse($order->created_at)->format('Y-m-d');
        })->map(function($dayOrders) {
            return $dayOrders->sum('cost');
        });

        $labels = [];
        $data = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d');
            $data[] = $dailyData[$dateKey] ?? 0;
            $currentDate->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
            'stats' => [
                'total_transactions' => $totalTransactions,
                'total_revenue' => number_format($totalRevenue, 2),
                'average_order' => number_format($averageOrder, 2),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
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

