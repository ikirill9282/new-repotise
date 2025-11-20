<div>
    <div class="mb-4 flex gap-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search by Title/Author..."
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Title</th>
                    <th class="text-left p-2">Author</th>
                    <th class="text-left p-2">Type</th>
                    <th class="text-right p-2">Views</th>
                    <th class="text-right p-2">Unique Views</th>
                    <th class="text-right p-2">Avg. Time</th>
                    <th class="text-right p-2">Comments</th>
                </tr>
            </thead>
            <tbody>
                @forelse($content as $item)
                    <tr class="border-b">
                        <td class="p-2">{{ $item['title'] ?? 'N/A' }}</td>
                        <td class="p-2">
                            <a href="{{ route('filament.admin.resources.users.index', ['tableSearch' => $item['author_id'] ?? '']) }}" class="text-primary-600 hover:underline">
                                {{ $item['author_name'] ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="p-2">{{ $item['type'] ?? 'Article' }}</td>
                        <td class="p-2 text-right">{{ number_format($item['views'] ?? 0) }}</td>
                        <td class="p-2 text-right">{{ number_format($item['unique_views'] ?? 0) }}</td>
                        <td class="p-2 text-right">{{ $item['avg_time_on_page'] ?? '0:00' }}</td>
                        <td class="p-2 text-right">{{ number_format($item['approved_comments'] ?? 0) }}</td>
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

