<div>
    <div class="mb-4 flex gap-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search by Order ID, User..."
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Order ID</th>
                    <th class="text-left p-2">User</th>
                    <th class="text-left p-2">Products</th>
                    <th class="text-right p-2">GMV</th>
                    <th class="text-right p-2">Referral Reward</th>
                    <th class="text-right p-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-b">
                        <td class="p-2">
                            <a href="{{ route('filament.admin.resources.orders.index', ['tableSearch' => $order->id]) }}" class="text-primary-600 hover:underline">
                                #{{ $order->id }}
                            </a>
                        </td>
                        <td class="p-2">
                            <a href="{{ route('filament.admin.resources.users.index', ['tableSearch' => $order->user_id]) }}" class="text-primary-600 hover:underline">
                                {{ $order->user->name ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="p-2">
                            {{ $order->order_products->count() }} item(s)
                        </td>
                        <td class="p-2 text-right">${{ number_format($order->cost, 2) }}</td>
                        <td class="p-2 text-right">${{ number_format($order->referal_reward ?? 0, 2) }}</td>
                        <td class="p-2 text-right">{{ $order->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            No data for selected period
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>

