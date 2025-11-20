<?php

namespace App\Filament\Pages\Analytics\Concerns;

use Illuminate\Support\Carbon;

trait HasDateRange
{
    protected function getStartDate(): Carbon
    {
        $startDate = request()->get('start_date');
        
        if ($startDate) {
            return Carbon::parse($startDate)->startOfDay();
        }
        
        $preset = request()->get('date_preset', 'last_30_days');
        
        return match($preset) {
            'today' => Carbon::today(),
            'yesterday' => Carbon::yesterday(),
            'last_7_days' => Carbon::now()->subDays(7)->startOfDay(),
            'last_30_days' => Carbon::now()->subDays(30)->startOfDay(),
            'this_month' => Carbon::now()->startOfMonth(),
            'last_90_days' => Carbon::now()->subDays(90)->startOfDay(),
            'this_year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };
    }

    protected function getEndDate(): Carbon
    {
        $endDate = request()->get('end_date');
        
        if ($endDate) {
            return Carbon::parse($endDate)->endOfDay();
        }
        
        $preset = request()->get('date_preset', 'last_30_days');
        
        return match($preset) {
            'today', 'yesterday' => Carbon::parse($this->getStartDate())->endOfDay(),
            default => Carbon::now()->endOfDay(),
        };
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

