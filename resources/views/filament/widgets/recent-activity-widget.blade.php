<x-filament-widgets::widget class="fi-recent-activity-widget">
    <x-filament::section>
        <x-slot name="heading">
            Recent Activity Feeds
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Recent Orders --}}
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recent Orders</h4>
                <div class="space-y-1 text-sm">
                    @forelse($this->getRecentOrders() as $order)
                        <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded">
                            <div class="font-medium">#{{ $order['id'] }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $order['product_name'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">{{ $order['buyer'] }} - ${{ number_format($order['amount'], 2) }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-600">{{ $order['time'] }}</div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-500">No recent orders</div>
                    @endforelse
                </div>
                <a href="{{ \App\Filament\Resources\TransactionResource::getUrl('index') }}" 
                   class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>

            {{-- Recent Registrations --}}
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recent Registrations</h4>
                <div class="space-y-1 text-sm">
                    @forelse($this->getRecentRegistrations() as $user)
                        <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded">
                            <div class="font-medium">@{{ $user['username'] }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $user['role'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-600">{{ $user['time'] }}</div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-500">No recent registrations</div>
                    @endforelse
                </div>
                <a href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}" 
                   class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>

            {{-- Recent Moderation Queue Items --}}
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Moderation Queue</h4>
                <div class="space-y-1 text-sm">
                    @forelse($this->getRecentModerationItems() as $item)
                        <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded">
                            <div class="font-medium">{{ $item['type'] }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($item['element'], 30) }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-600">{{ $item['time'] }}</div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-500">No items in queue</div>
                    @endforelse
                </div>
                <a href="{{ \App\Filament\Resources\ModerationQueueResource::getUrl('index') }}" 
                   class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>

            {{-- Recent Payouts --}}
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recent Payouts</h4>
                <div class="space-y-1 text-sm">
                    @forelse($this->getRecentPayouts() as $payout)
                        <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded">
                            <div class="font-medium">{{ \Illuminate\Support\Str::limit($payout['id'], 20) }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $payout['seller'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">${{ number_format($payout['amount'], 2) }} - {{ $payout['status'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-600">{{ $payout['time'] }}</div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-500">No recent payouts</div>
                    @endforelse
                </div>
                <a href="{{ \App\Filament\Resources\PayoutResource::getUrl('index') }}" 
                   class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>

            {{-- Recent Content --}}
            <div class="space-y-2">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recent Content</h4>
                <div class="space-y-1 text-sm">
                    @forelse($this->getRecentContent() as $content)
                        <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded">
                            <div class="font-medium">{{ \Illuminate\Support\Str::limit($content['title'], 25) }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $content['author'] }} - {{ $content['type'] }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-600">{{ $content['time'] }}</div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-500">No recent content</div>
                    @endforelse
                </div>
                <a href="{{ \App\Filament\Resources\ArticleResource::getUrl('index') }}" 
                   class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

