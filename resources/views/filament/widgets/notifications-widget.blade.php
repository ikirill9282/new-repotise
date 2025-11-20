<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Notifications / Alerts
        </x-slot>

        <div class="space-y-2">
            @forelse($this->getNotifications() as $notification)
                <div class="flex items-start gap-2 p-2 rounded border {{ $notification->read ? 'bg-gray-50' : 'bg-white' }}">
                    <div class="flex-shrink-0 mt-0.5">
                        @if($notification->type === 'complaint')
                            <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-warning-500" />
                        @elseif($notification->type === 'moderation')
                            <x-heroicon-o-document-text class="w-4 h-4 text-info-500" />
                        @elseif($notification->type === 'system_error')
                            <x-heroicon-o-x-circle class="w-4 h-4 text-danger-500" />
                        @else
                            <x-heroicon-o-bell class="w-4 h-4 text-gray-500" />
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900">{{ $notification->title }}</p>
                        @if($notification->message)
                            <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($notification->message, 60) }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex-shrink-0 flex gap-1">
                        @if($notification->url)
                            <a href="{{ $notification->url }}" class="text-xs text-primary-600 hover:text-primary-700">
                                View
                            </a>
                        @endif
                        <button 
                            wire:click="markAsRead({{ $notification->id }})"
                            class="text-xs text-gray-500 hover:text-gray-700"
                        >
                            ✓
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-xs text-gray-500 text-center py-2">No notifications</p>
            @endforelse
        </div>

        @if($this->getUnreadCount() > 10)
            <div class="mt-2 text-center">
                <a href="#" class="text-xs text-primary-600 hover:text-primary-700">
                    View all ({{ $this->getUnreadCount() }})
                </a>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

