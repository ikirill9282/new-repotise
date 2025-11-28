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
        // Обновляем даты из параметров события или из session
        $newStartDate = $start ?? session('dashboard_start_date');
        $newEndDate = $end ?? session('dashboard_end_date');
        
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
        
        // Принудительно обновляем виджет
        $this->dispatch('$refresh');
    }

    protected function getStartDate(): Carbon
    {
        // ВАЖНО: Всегда читаем из session первым делом (самый актуальный источник)
        $startDate = session('dashboard_start_date');
        
        if ($startDate) {
            // Обновляем свойство виджета для синхронизации
            $this->dashboardStartDate = $startDate;
            return Carbon::parse($startDate)->startOfDay();
        }
        
        // Затем из свойства виджета
        if ($this->dashboardStartDate) {
            return Carbon::parse($this->dashboardStartDate)->startOfDay();
        }
        
        // Fallback на последние 30 дней
        return Carbon::now()->subDays(30)->startOfDay();
    }

    protected function getEndDate(): Carbon
    {
        // ВАЖНО: Всегда читаем из session первым делом (самый актуальный источник)
        $endDate = session('dashboard_end_date');
        
        if ($endDate) {
            // Обновляем свойство виджета для синхронизации
            $this->dashboardEndDate = $endDate;
            return Carbon::parse($endDate)->endOfDay();
        }
        
        // Затем из свойства виджета
        if ($this->dashboardEndDate) {
            return Carbon::parse($this->dashboardEndDate)->endOfDay();
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

