<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Report;
use App\Models\AdminNotification;

class ComplaintsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        // Новые жалобы
        $newComplaints = 0;
        $inProgress = 0;
        $resolved = 0;
        
        try {
            $newComplaints = Report::query()
                ->where('status', Report::STATUS_NEW)
                ->count();

            // В работе
            $inProgress = Report::query()
                ->where('status', Report::STATUS_IN_PROGRESS)
                ->count();

            // Решённые
            $resolved = Report::query()
                ->where('status', Report::STATUS_RESOLVED)
                ->count();
        } catch (\Exception $e) {
            // Если таблица не существует или поле status отсутствует, используем значения по умолчанию
        }

        // Системные ошибки (из уведомлений)
        $systemErrors = 0;
        try {
            $systemErrors = AdminNotification::query()
                ->where('type', AdminNotification::TYPE_SYSTEM_ERROR)
                ->where('read', false)
                ->count();
        } catch (\Exception $e) {
            // Если таблица не существует, используем 0
        }

        // Проверяем существование маршрута для жалоб
        $reportsUrl = null;
        try {
            $reportsUrl = route('filament.admin.resources.reports.index');
        } catch (\Illuminate\Routing\Exceptions\RouteNotFoundException $e) {
            // Маршрут не существует, оставляем null
        } catch (\Exception $e) {
            // Другие ошибки - также оставляем null
        }

        $newComplaintsStat = Stat::make('New Complaints', number_format($newComplaints))
            ->description('Awaiting review')
            ->descriptionIcon('heroicon-m-exclamation-circle')
            ->color('warning')
            ->icon('heroicon-o-exclamation-triangle');
        
        if ($reportsUrl) {
            $newComplaintsStat->url($reportsUrl);
        }

        return [
            $newComplaintsStat,
            Stat::make('System Errors', number_format($systemErrors))
                ->description('Critical issues')
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}

