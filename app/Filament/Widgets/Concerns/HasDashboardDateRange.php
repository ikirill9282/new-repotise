<?php

namespace App\Filament\Widgets\Concerns;

use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

trait HasDashboardDateRange
{
    public ?string $dashboardStartDate = null;
    public ?string $dashboardEndDate = null;
    
    // Публичное свойство для принудительного обновления виджета
    public int $dateRangeUpdateTrigger = 0;

    public function mountHasDashboardDateRange(): void
    {
        // Инициализируем даты из session или request при монтировании
        $this->dashboardStartDate = session('dashboard_start_date') ?? request()->get('dashboard_start_date');
        $this->dashboardEndDate = session('dashboard_end_date') ?? request()->get('dashboard_end_date');
    }

    #[On('dashboard-date-range-updated')]
    public function refreshDateRange(?string $start = null, ?string $end = null): void
    {
        // Обновляем даты из параметров события или из request/session
        $newStartDate = $start ?? request()->get('dashboard_start_date') ?? session('dashboard_start_date');
        $newEndDate = $end ?? request()->get('dashboard_end_date') ?? session('dashboard_end_date');
        
        // Всегда обновляем даты и триггер для принудительного обновления
        $this->dashboardStartDate = $newStartDate;
        $this->dashboardEndDate = $newEndDate;
        $this->dateRangeUpdateTrigger++;
        
        // Очищаем кеш если есть
        if (property_exists($this, 'cachedStats')) {
            unset($this->cachedStats);
        }
        
        // Очищаем кеш данных графика если есть
        if (property_exists($this, 'cachedData')) {
            unset($this->cachedData);
        }
    }

    protected function getStartDate(): Carbon
    {
        // Сначала пытаемся получить из свойства виджета
        if ($this->dashboardStartDate) {
            return Carbon::parse($this->dashboardStartDate)->startOfDay();
        }
        
        // Затем из session (устанавливаются Dashboard)
        $startDate = session('dashboard_start_date');
        
        if ($startDate) {
            return Carbon::parse($startDate)->startOfDay();
        }
        
        // Затем из request параметров
        $startDate = request()->get('dashboard_start_date');
        
        if ($startDate) {
            return Carbon::parse($startDate)->startOfDay();
        }
        
        // Fallback на последние 30 дней
        return Carbon::now()->subDays(30)->startOfDay();
    }

    protected function getEndDate(): Carbon
    {
        // Сначала пытаемся получить из свойства виджета
        if ($this->dashboardEndDate) {
            return Carbon::parse($this->dashboardEndDate)->endOfDay();
        }
        
        // Затем из session (устанавливаются Dashboard)
        $endDate = session('dashboard_end_date');
        
        if ($endDate) {
            return Carbon::parse($endDate)->endOfDay();
        }
        
        // Затем из request параметров
        $endDate = request()->get('dashboard_end_date');
        
        if ($endDate) {
            return Carbon::parse($endDate)->endOfDay();
        }
        
        // Fallback на сегодня
        return Carbon::now()->endOfDay();
    }

    protected function getPreviousPeriodStartDate(): Carbon
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $daysDiff = $startDate->diffInDays($endDate);
        
        return $startDate->copy()->subDays($daysDiff + 1)->startOfDay();
    }

    protected function getPreviousPeriodEndDate(): Carbon
    {
        $startDate = $this->getStartDate();
        
        return $startDate->copy()->subDay()->endOfDay();
    }

    protected function calculateChange($current, $previous): ?float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : null;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }
}

