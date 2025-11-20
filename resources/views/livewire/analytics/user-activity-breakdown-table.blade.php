<div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Role</th>
                    <th class="text-right p-2">Active Users</th>
                    <th class="text-right p-2">Avg. Sessions per User</th>
                    <th class="text-right p-2">Avg. Session Duration</th>
                </tr>
            </thead>
            <tbody>
                @forelse($breakdown as $item)
                    <tr class="border-b">
                        <td class="p-2">{{ $item['role'] ?? 'N/A' }}</td>
                        <td class="p-2 text-right">{{ number_format($item['active_users'] ?? 0) }}</td>
                        <td class="p-2 text-right">{{ number_format($item['avg_sessions_per_user'] ?? 0, 2) }}</td>
                        <td class="p-2 text-right">{{ $item['avg_session_duration'] ?? '0:00' }}</td>
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

