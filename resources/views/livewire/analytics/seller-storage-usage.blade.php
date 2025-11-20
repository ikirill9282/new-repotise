<div>
    <div class="mb-4 flex gap-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search by Seller..."
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
        />
        <select wire:model.live="filterLevel" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
            <option value="">All Levels</option>
            <option value="warning">Warning (>80%)</option>
            <option value="critical">Critical (>95%)</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Seller</th>
                    <th class="text-right p-2">Used Storage</th>
                    <th class="text-right p-2">Total Storage</th>
                    <th class="text-right p-2">Usage %</th>
                    <th class="text-right p-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sellers as $seller)
                    <tr class="border-b">
                        <td class="p-2">{{ $seller['name'] ?? 'N/A' }}</td>
                        <td class="p-2 text-right">{{ $seller['used'] ?? '0 MB' }}</td>
                        <td class="p-2 text-right">{{ $seller['total'] ?? '0 MB' }}</td>
                        <td class="p-2 text-right">{{ number_format($seller['percentage'] ?? 0, 2) }}%</td>
                        <td class="p-2 text-right">
                            @if(($seller['percentage'] ?? 0) > 95)
                                <span class="text-red-600">Critical</span>
                            @elseif(($seller['percentage'] ?? 0) > 80)
                                <span class="text-yellow-600">Warning</span>
                            @else
                                <span class="text-green-600">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            No data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

