<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityWidget extends ChartWidget
{
    protected static ?string $heading = 'User Activity';

    protected static ?int $sort = 6;

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

        // Агрегируем активность по дням
        // Логины - используем updated_at как приблизительный индикатор активности
        $logins = collect();
        try {
            $logins = User::query()
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->get()
                ->keyBy('date');
        } catch (\Exception $e) {
            // Если таблица не существует или поле недоступно, просто пропускаем
        }

        // Покупки
        $orders = collect();
        try {
            $orders = Order::query()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->get()
                ->keyBy('date');
        } catch (\Exception $e) {
            // Если таблица не существует, просто пропускаем
        }

        // Создание контента (статьи)
        $articles = collect();
        try {
            $articles = DB::table('articles')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->get()
                ->keyBy('date');
        } catch (\Exception $e) {
            // Если таблица не существует, просто пропускаем
        }

        // Комментарии
        $comments = collect();
        try {
            $comments = DB::table('comments')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->get()
                ->keyBy('date');
        } catch (\Exception $e) {
            // Если таблица не существует, просто пропускаем
        }

        $labels = [];
        $loginData = [];
        $orderData = [];
        $articleData = [];
        $commentData = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d');
            
            $loginData[] = $logins->has($dateKey) ? (int)$logins[$dateKey]->count : 0;
            $orderData[] = $orders->has($dateKey) ? (int)$orders[$dateKey]->count : 0;
            $articleData[] = $articles->has($dateKey) ? (int)$articles[$dateKey]->count : 0;
            $commentData[] = $comments->has($dateKey) ? (int)$comments[$dateKey]->count : 0;
            
            $currentDate->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Logins',
                    'data' => $loginData,
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Orders',
                    'data' => $orderData,
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'Articles',
                    'data' => $articleData,
                    'borderColor' => 'rgb(245, 158, 11)',
                ],
                [
                    'label' => 'Comments',
                    'data' => $commentData,
                    'borderColor' => 'rgb(239, 68, 68)',
                ],
            ],
            'labels' => $labels,
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

