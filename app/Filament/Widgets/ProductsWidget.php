<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Enums\Status;
use Illuminate\Support\Carbon;

class ProductsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $period = $this->getPeriod();
        $startDate = $period['start'];
        $endDate = $period['end'];

        // Активные товары
        $activeProducts = Product::query()
            ->where('status_id', Status::ACTIVE)
            ->count();

        // Товары на модерации
        $pendingProducts = Product::query()
            ->where('status_id', Status::PENDING)
            ->count();

        return [
            Stat::make('Active Products', number_format($activeProducts))
                ->description('Published')
                ->color('success')
                ->icon('heroicon-o-shopping-bag')
                ->url(route('filament.admin.resources.products.index', ['tableFilters' => ['status_id' => ['value' => Status::ACTIVE]]])),
            Stat::make('Pending', number_format($pendingProducts))
                ->description('Awaiting review')
                ->color('warning')
                ->icon('heroicon-o-exclamation-triangle')
                ->url(route('filament.admin.resources.products.index', ['tableFilters' => ['status_id' => ['value' => Status::PENDING]]])),
        ];
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

