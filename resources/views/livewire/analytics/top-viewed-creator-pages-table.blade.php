<div>
    <div class="mb-4 flex gap-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search by Creator..."
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Creator</th>
                    <th class="text-right p-2">Pageviews</th>
                    <th class="text-right p-2">Unique Pageviews</th>
                    <th class="text-right p-2">Avg. Time on Page</th>
                </tr>
            </thead>
            <tbody>
                @forelse($creators as $creator)
                    <tr class="border-b">
                        <td class="p-2">{{ $creator['name'] ?? 'N/A' }}</td>
                        <td class="p-2 text-right">{{ number_format($creator['pageviews'] ?? 0) }}</td>
                        <td class="p-2 text-right">{{ number_format($creator['unique_pageviews'] ?? 0) }}</td>
                        <td class="p-2 text-right">{{ $creator['avg_time'] ?? '0:00' }}</td>
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
</div>

