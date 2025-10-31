<div>
    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Refunds Summary</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Total Refunds:</div>
            <div class="text-nowrap">{{ number_format($summary['total_refunds']) }}</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Refund Rate:</div>
            <div class="text-nowrap">
              {{ $summary['refund_rate'] > 0 ? number_format($summary['refund_rate'], 2) . '%' : '—' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($rows->isEmpty())
      <div class="text-lg text-center text-gray">You haven't received any refund requests yet.</div>
    @else
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
        <div class="font-bold text-lg px-1 mb-4">Recent Refunds</div>
        <table class="table">
          <thead>
            <tr class="">
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date & Time</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Order #</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product Name</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">
                <div class="relative inline-block !pr-6">
                  Act on Request
                  <x-tooltip message="Open the request in the sales dashboard to respond." />
                </div>
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $refund)
              @php
                $order = $refund->order;
                $product = $refund->orderProduct?->product;
                $date = $refund->created_at;
                $formattedDate = $date
                  ? $date->copy()->timezone(config('app.timezone'))->format('m.d.Y H:i')
                  : '—';
                $status = ucfirst(str_replace('_', ' ', $refund->status));
                $reason = $refund->reason ?? $refund->details;
              @endphp
              <tr>
                <td class="!border-b-gray/15 !py-4 text-nowrap">{{ $formattedDate }}</td>
                <td class="!border-b-gray/15 !py-4">
                  {{ $order ? '#'.$order->id : '—' }}
                </td>
                <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">
                  {{ $product?->title ?? 'Product removed' }}
                </td>
                <td class="!border-b-gray/15 !py-4">
                  @if($refund->status === 'pending')
                    <x-link href="{{ route('profile.sales') }}">Review request</x-link>
                  @else
                    <div class="flex flex-col gap-1">
                      <span class="text-gray">{{ $status }}</span>
                      @if($reason)
                        <span class="text-xs text-gray">{{ \Illuminate\Support\Str::limit($reason, 80) }}</span>
                      @endif
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot></tfoot>
        </table>
      </div>

      @if($hasMore)
        <div class="text-right">
          <x-btn href="{{ route('profile.sales') }}" outlined class="!border-active hover:!border-second !w-auto !px-12">
            View All Refunds
          </x-btn>
        </div>
      @endif
    @endif
</div>
