<div id="product-analytics">
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}
    <x-card size="sm">
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
        <div class="flex justify-start items-start xl:items-center flex-col xl:flex-row !gap-4 xl:!gap-8 !mb-10">
          <div class="font-bold text-2xl">Filters</div>
          <div class="flex justify-start items-start sm:items-center !gap-4 2xl:!gap-8 flex-col sm:flex-row">
            <div class="block">
              <label class="text-gray" for="product-analytics-status">Product Status:</label>
              <select
                class="tg-select"
                wire:model.live="statusFilter"
                id="product-analytics-status"
                >
                <option value="">All Statuses</option>
                @foreach($statusOptions as $value => $label)
                  <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        @if($rows->isEmpty())
          <div class="py-6 text-center text-gray">No product analytics available for this period.</div>
        @else
          <table class="table text-sm md:text-base">
              <thead>
                <tr class="">
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product Name</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Image</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Views</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Units Sold</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Conversion Rate</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Average Rating</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Total Revenue</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rows as $row)
                  @php
                    $product = $row['product'];
                    $preview = $product?->preview?->image;
                  @endphp
                  <tr>
                    <td class="!border-b-gray/15 !py-4 min-w-2xs">
                      @if($product)
                        <x-link :border="false" :href="$product->makeUrl()">{{ $product->title }}</x-link>
                      @else
                        <span class="text-gray">Product removed</span>
                      @endif
                    </td>
                    <td class="!border-b-gray/15 !py-4 !text-gray">
                      <div class="!w-28 !h-18 rounded overflow-hidden bg-light flex items-center justify-center">
                        @if($preview)
                          <img class="object-cover w-full h-full" src="{{ $preview }}" alt="{{ $product?->title ?? 'Product' }}">
                        @else
                          <span class="text-sm text-gray">No image</span>
                        @endif
                      </div>
                    </td>
                    <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">{{ number_format($row['views']) }}</td>
                    <td class="!border-b-gray/15 !py-4 text-nowrap">{{ number_format($row['units_sold']) }}</td>
                    <td class="!border-b-gray/15 !py-4 ">{{ number_format($row['conversion_rate'], 2) }}%</td>
                    <td class="!border-b-gray/15 !py-4 ">
                      <div class="flex justify-start items-center h-full gap-1">
                        <span class="text-yellow">@include('icons.star')</span>
                        <span>{{ number_format($row['average_rating'], 1) }}</span>
                      </div>
                    </td>
                    <td class="!border-b-gray/15 !py-4 ">{{ currency($row['gross_revenue']) }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot></tfoot>
            </table>
        @endif
      </div>
    </x-card>
</div>
