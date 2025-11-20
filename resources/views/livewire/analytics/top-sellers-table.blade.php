<div>
    <div class="mb-4 flex gap-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search by Seller..."
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Seller</th>
                    <th class="text-right p-2">GMV</th>
                    <th class="text-right p-2">Sales Count</th>
                    <th class="text-right p-2">Platform Commission</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sellers as $seller)
                    <tr class="border-b">
                        <td class="p-2">
                            <a href="{{ route('filament.admin.resources.users.index', ['tableSearch' => $seller['id'] ?? '']) }}" class="text-primary-600 hover:underline">
                                {{ $seller['name'] ?? $seller['username'] ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="p-2 text-right">${{ number_format($seller['gmv'] ?? 0, 2) }}</td>
                        <td class="p-2 text-right">{{ number_format($seller['sales_count'] ?? 0) }}</td>
                        <td class="p-2 text-right">${{ number_format($seller['platform_commission'] ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">
                            No data for selected period
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        @if($total > $perPage)
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Showing {{ (request()->get('page', 1) - 1) * $perPage + 1 }} to {{ min(request()->get('page', 1) * $perPage, $total) }} of {{ $total }} results</span>
                <div class="flex gap-2">
                    @if(request()->get('page', 1) > 1)
                        <a href="?page={{ request()->get('page', 1) - 1 }}" class="px-4 py-2 bg-gray-200 rounded">Previous</a>
                    @endif
                    @if(request()->get('page', 1) * $perPage < $total)
                        <a href="?page={{ request()->get('page', 1) + 1 }}" class="px-4 py-2 bg-gray-200 rounded">Next</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

