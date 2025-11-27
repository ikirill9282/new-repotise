<div class="space-y-2">
    @forelse($payouts as $payout)
        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <div class="font-medium text-sm">{{ $payout->payout_id ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        {{ $payout->payout_method ?? 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                        {{ $payout->created_at->format('M d, Y H:i') }}
                        @if($payout->processed_at)
                            <br>Processed: {{ $payout->processed_at->format('M d, Y H:i') }}
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-semibold text-sm">${{ number_format($payout->amount, 2) }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        Status: 
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if($payout->status === 'completed' || $payout->status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($payout->status === 'failed' || $payout->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @endif">
                            {{ ucfirst($payout->status) }}
                        </span>
                    </div>
                    @if($payout->failure_message)
                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                            {{ $payout->failure_message }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No payouts found.</div>
    @endforelse
</div>

