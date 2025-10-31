<div>
    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Sales Overview</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Total Revenue:</div>
            <div class="text-nowrap">{{ currency($summary['total_revenue']) }}</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Product Sales:</div>
            <div class="text-nowrap">{{ currency($summary['product_sales']) }}</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Projected Recurring Revenue:</div>
            <div class="text-nowrap">{{ currency($summary['recurring_revenue']) }}</div>
          </div>
        </div>
      </div>
    </div>

    @if($rows->isEmpty())
      <div class="text-lg text-center text-gray">There are no sales yet.</div>
    @else
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
        <table class="table">
          <thead>
            <tr class="">
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date & Time</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Order #</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product Name</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Net Earnings</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $share)
              @php
                $order = $share->order;
                $product = $share->product;
                $date = $order?->created_at ?? $share->created_at;
                $formattedDate = $date
                  ? $date->copy()->timezone(config('app.timezone'))->format('m.d.Y H:i')
                  : '—';
              @endphp
              <tr>
                <td class="!border-b-gray/15 !py-4 !text-gray">{{ $formattedDate }}</td>
                <td class="!border-b-gray/15 !py-4 !text-gray">
                  {{ $order ? '#'.$order->id : '—' }}
                </td>
                <td class="!border-b-gray/15 !py-4 text-nowrap">
                  <span title="{{ $product?->title }}">{{ $product?->title ?? 'Product removed' }}</span>
                </td>
                <td class="!border-b-gray/15 !py-4">{{ currency((float) $share->author_amount) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot></tfoot>
        </table>
      </div>

      @if($hasMore)
        <div class="text-right">
          <x-btn wire:click.prevent="loadAll" outlined class="!border-active hover:!border-second !w-auto !px-12">View All Sales</x-btn>
        </div>
      @endif
    @endif
</div>
