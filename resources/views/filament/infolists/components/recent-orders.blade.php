<div class="space-y-2">
    @forelse($orders as $order)
        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <div class="font-medium text-sm">Order #{{ $order->id }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        @if($order->order_products->isNotEmpty())
                            {{ $order->order_products->first()->product->title ?? 'N/A' }}
                            @if($order->order_products->count() > 1)
                                + {{ $order->order_products->count() - 1 }} more
                            @endif
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                        {{ $order->created_at->format('M d, Y H:i') }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-semibold text-sm">${{ number_format($order->cost, 2) }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        Status: 
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if($order->status_id >= 2) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @endif">
                            {{ $order->status_id >= 2 ? 'Paid' : 'Pending' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No orders found.</div>
    @endforelse
</div>

