<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UsersWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $period = $this->getPeriod();
        $startDate = $period['start'];
        $endDate = $period['end'];

        // Активные пользователи (с активностью за период)
        $activeUsers = User::query()
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereHas('orders', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->orWhereHas('articles', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->orWhereHas('comments', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->orWhereBetween('updated_at', [$startDate, $endDate])
                ->orWhereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();

        // Новые пользователи за период
        $newUsers = User::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            Stat::make('Active Users', number_format($activeUsers))
                ->description('Active in period')
                ->color('success')
                ->icon('heroicon-o-user-group'),
            Stat::make('New Users', number_format($newUsers))
                ->description('New in period')
                ->color('primary')
                ->icon('heroicon-o-user-plus'),
        ];
    }

    protected function getPeriod(): array
    {
        // По умолчанию последние 30 дней
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);

        // TODO: Получать из фильтров дашборда
        return [
            'start' => $startDate,
            'end' => $endDate,
        ];
    }
}

