<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\AdminNotification;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class NotificationsWidget extends Widget
{
    protected static string $view = 'filament.widgets.notifications-widget';

    protected static ?int $sort = 8;

    protected int | string | array $columnSpan = 'full';

    public function getNotifications()
    {
        try {
            return AdminNotification::query()
                ->where('read', false)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            // Если таблица не существует, возвращаем пустую коллекцию
            return collect();
        }
    }

    public function getUnreadCount()
    {
        try {
            return AdminNotification::query()->where('read', false)->count();
        } catch (\Exception $e) {
            // Если таблица не существует, возвращаем 0
            return 0;
        }
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = AdminNotification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
        }
    }
}

