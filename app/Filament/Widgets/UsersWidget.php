<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\User;
use App\Filament\Resources\UserResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UsersWidget extends BaseWidget
{
    use HasDashboardDateRange;

    protected static ?int $sort = 1;

    public function mount(): void
    {
        $this->mountHasDashboardDateRange();
    }

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        // ВАЖНО: Используем trigger для принудительного обновления
        $trigger = $this->dateRangeUpdateTrigger ?? 0;
        // Принудительно читаем из session чтобы получить актуальные данные
        $this->dashboardStartDate = session('dashboard_start_date');
        $this->dashboardEndDate = session('dashboard_end_date');
        
        // Читаем даты из session каждый раз при вызове метода
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

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

        // Расчет % изменения для новых пользователей
        $prevStart = $this->getPreviousPeriodStartDate();
        $prevEnd = $this->getPreviousPeriodEndDate();
        $prevNewUsers = User::query()
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();
        $newUsersChange = $this->calculateChange($newUsers, $prevNewUsers);

        return [
            Stat::make('Active Users', number_format($activeUsers))
                ->description('Active in period')
                ->color('success')
                ->icon('heroicon-o-user-group'),
            Stat::make('New Users', number_format($newUsers))
                ->description($newUsersChange !== null 
                    ? ($newUsersChange >= 0 ? '+' : '') . number_format($newUsersChange, 1) . '% vs previous period'
                    : 'New in period'
                )
                ->descriptionIcon($newUsersChange !== null && $newUsersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($newUsersChange !== null && $newUsersChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-user-plus')
                ->url(UserResource::getUrl('index')),
        ];
    }
}

