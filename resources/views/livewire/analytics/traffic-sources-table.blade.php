<div>
    <div class="mb-4 flex gap-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search by Source/Medium..."
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Source</th>
                    <th class="text-left p-2">Medium</th>
                    <th class="text-right p-2">Sessions</th>
                    <th class="text-right p-2">Users</th>
                    <th class="text-right p-2">New Users</th>
                    <th class="text-right p-2">Bounce Rate</th>
                    <th class="text-right p-2">Avg. Session Duration</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sources as $source)
                    <tr class="border-b">
                        <td class="p-2">{{ $source['source'] ?? 'N/A' }}</td>
                        <td class="p-2">{{ $source['medium'] ?? 'N/A' }}</td>
                        <td class="p-2 text-right">{{ number_format($source['sessions'] ?? 0) }}</td>
                        <td class="p-2 text-right">{{ number_format($source['users'] ?? 0) }}</td>
                        <td class="p-2 text-right">{{ number_format($source['new_users'] ?? 0) }}</td>
                        <td class="p-2 text-right">{{ number_format(($source['bounce_rate'] ?? 0) * 100, 2) }}%</td>
                        <td class="p-2 text-right">{{ $source['avg_duration'] ?? '0:00' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">
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
