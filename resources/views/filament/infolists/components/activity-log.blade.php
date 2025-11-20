<div class="overflow-x-auto">
  <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
      <tr>
        <th scope="col" class="px-4 py-3">Date & Time</th>
        <th scope="col" class="px-4 py-3">Action</th>
        <th scope="col" class="px-4 py-3">Description</th>
        <th scope="col" class="px-4 py-3">Initiator</th>
        <th scope="col" class="px-4 py-3">IP Address</th>
      </tr>
    </thead>
    <tbody>
      @foreach($histories as $history)
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <td class="px-4 py-3">
            {{ $history->created_at->format('Y-m-d H:i:s') }}
          </td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 text-xs font-semibold rounded-full 
              {{ match($history->type) {
                'success' => 'bg-green-100 text-green-800',
                'error' => 'bg-red-100 text-red-800',
                'warning' => 'bg-yellow-100 text-yellow-800',
                'info' => 'bg-blue-100 text-blue-800',
                default => 'bg-gray-100 text-gray-800',
              } }}">
              {{ $history->action }}
            </span>
          </td>
          <td class="px-4 py-3">
            {{ $history->message }}
            @if($history->old_value && $history->value)
              <div class="text-xs text-gray-500 mt-1">
                Changed from: <strong>{{ $history->old_value }}</strong> to: <strong>{{ $history->value }}</strong>
              </div>
            @endif
          </td>
          <td class="px-4 py-3">
            {{ $history->initer?->username ?? 'System' }}
          </td>
          <td class="px-4 py-3">
            {{ $history->ip_address ?? 'N/A' }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  
  @if($histories->isEmpty())
    <div class="text-center py-8 text-gray-500">
      No activity recorded.
    </div>
  @endif
</div>

