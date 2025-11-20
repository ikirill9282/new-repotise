<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\UsersWidget;
use App\Filament\Widgets\ProductsWidget;
use App\Filament\Widgets\TransactionsWidget;
use App\Filament\Widgets\ComplaintsWidget;
use App\Filament\Widgets\ModerationWidget;
use App\Filament\Widgets\ActivityWidget;
use App\Filament\Widgets\RevenueWidget;
use App\Filament\Widgets\NotificationsWidget;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?int $navigationSort = -1;

    protected static ?string $title = 'Dashboard';

    protected function getUserWidgets(): array
    {
        return [
            UsersWidget::class,
            ActivityWidget::class,
        ];
    }

    protected function getProductWidgets(): array
    {
        return [
            ProductsWidget::class,
            TransactionsWidget::class,
        ];
    }

    protected function getSystemWidgets(): array
    {
        return [
            ComplaintsWidget::class,
            ModerationWidget::class,
            NotificationsWidget::class,
        ];
    }
}
