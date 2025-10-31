<div>
    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Product</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Product Page Views:</div>
            <div class="text-nowrap">{{ number_format($summary['views']) }}</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Average Rating:</div>
            <div class="text-nowrap">
              {{ $summary['average_rating'] > 0 ? number_format($summary['average_rating'], 2) : '—' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($rows->isEmpty())
      <div class="text-lg text-center text-gray">You haven't published any products yet.</div>
    @else
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
        <div class="font-bold text-lg px-1 mb-4">Top Products</div>
        <table class="table">
          <thead>
            <tr class="">
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product Name</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Image</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Units Sold</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Total Revenue</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $product)
              @php
                $preview = $product->preview?->image;
                $unitsSold = (int) ($product->units_sold ?? 0);
                $totalRevenue = (float) ($product->total_revenue ?? 0);
              @endphp
              <tr>
                <td class="!border-b-gray/15 !py-4 text-nowrap">
                  <x-link :href="$product->makeUrl()" class="!border-0">{{ $product->title }}</x-link>
                </td>
                <td class="!border-b-gray/15 !py-4 !text-gray">
                  <div class="w-36 h-18 rounded overflow-hidden bg-light/60 flex items-center justify-center">
                    @if($preview)
                      <img class="w-full h-full object-cover" src="{{ $preview }}" alt="{{ $product->title }}">
                    @else
                      <span class="text-sm text-gray">No image</span>
                    @endif
                  </div>
                </td>
                <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">
                  {{ number_format($unitsSold) }}
                </td>
                <td class="!border-b-gray/15 !py-4">
                  {{ currency($totalRevenue) }}
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
            View All Products Analytics
          </x-btn>
        </div>
      @endif
    @endif
</div>
