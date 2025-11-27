<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\Product;
use App\Enums\Status;
use App\Filament\Resources\ProductResource;

class ProductsWidget extends BaseWidget
{
    use HasDashboardDateRange;

    protected static ?int $sort = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        // Активные товары (не зависит от периода)
        $activeProducts = Product::query()
            ->where('status_id', Status::ACTIVE)
            ->count();

        // Товары на модерации (не зависит от периода)
        $pendingProducts = Product::query()
            ->where('status_id', Status::PENDING)
            ->count();

        return [
            Stat::make('Active Products', number_format($activeProducts))
                ->description('Published')
                ->color('success')
                ->icon('heroicon-o-shopping-bag')
                ->url(ProductResource::getUrl('index', ['tableFilters' => ['status_id' => ['value' => Status::ACTIVE]]])),
            Stat::make('Pending', number_format($pendingProducts))
                ->description('Awaiting review')
                ->color('warning')
                ->icon('heroicon-o-exclamation-triangle')
                ->url(ProductResource::getUrl('index', ['tableFilters' => ['status_id' => ['value' => Status::PENDING]]])),
        ];
    }
}

