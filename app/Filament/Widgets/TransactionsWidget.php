<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\Order;
use App\Enums\Order as EnumsOrder;
use App\Filament\Resources\TransactionResource;
use Illuminate\Support\Carbon;

class TransactionsWidget extends ChartWidget
{
    use HasDashboardDateRange;

    protected static ?string $heading = 'Transactions';

    public function mount(): void
    {
        $this->mountHasDashboardDateRange();
    }

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getChartHeight(): ?int
    {
        return 180;
    }

    protected function getData(): array
    {
        // Используем trigger для принудительного обновления
        $trigger = $this->dateRangeUpdateTrigger ?? 0;
        
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $orders = Order::query()
            ->where('status_id', '>=', EnumsOrder::PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalTransactions = $orders->count();
        $totalRevenue = (float) ($orders->sum('cost') ?? 0);
        $averageOrder = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Группировка по дням для графика
        $dailyData = $orders->groupBy(function($order) {
            return Carbon::parse($order->created_at)->format('Y-m-d');
        })->map(function($dayOrders) {
            return (float) ($dayOrders->sum('cost') ?? 0);
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

    public function getHeading(): string
    {
        $data = $this->getData();
        $stats = $data['stats'] ?? [];
        $totalTransactions = $stats['total_transactions'] ?? 0;
        $totalRevenue = $stats['total_revenue'] ?? '0.00';
        
        return "Transactions ({$totalTransactions}) - Total: \${$totalRevenue}";
    }
}

