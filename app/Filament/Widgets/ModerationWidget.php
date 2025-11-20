<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Article;
use App\Models\Review;
use App\Enums\Status;

class ModerationWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        // Товары на модерации
        $pendingProducts = Product::query()
            ->where('status_id', Status::PENDING)
            ->count();

        // Статьи на модерации
        $pendingArticles = Article::query()
            ->where('status_id', Status::PENDING)
            ->count();

        // Отзывы на модерации
        $pendingReviews = Review::query()
            ->where('status_id', Status::PENDING)
            ->count();

        $total = $pendingProducts + $pendingArticles + $pendingReviews;

        return [
            Stat::make('Total Pending', number_format($total))
                ->description('All moderation tasks')
                ->color('danger')
                ->icon('heroicon-o-clock'),
            Stat::make('Products', number_format($pendingProducts))
                ->description('Products queue')
                ->color('warning')
                ->icon('heroicon-o-shopping-bag')
                ->url(route('filament.admin.resources.products.index', ['tableFilters' => ['status_id' => ['value' => Status::PENDING]]])),
        ];
    }
}

