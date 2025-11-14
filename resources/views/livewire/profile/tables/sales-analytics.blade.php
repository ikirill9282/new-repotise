<div id="sales-analytics">
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
              <label class="text-gray" for="sales-analytics-status">Payment Status:</label>
              <select
                class="tg-select"
                wire:model.live="paymentStatus"
                id="sales-analytics-status"
                >
                <option value="">All Statuses</option>
                @foreach($statuses as $value => $label)
                  <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="block">
              <label class="text-gray" for="sales-analytics-product">Product:</label>
              <select 
                class="tg-select"
                wire:model.live="productFilter"
                id="sales-analytics-product"
                >
                <option value="">All Products</option>
                @foreach($productOptions as $product)
                  <option value="{{ $product->id }}">{{ $product->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="block">
              <label class="text-gray" for="sales-analytics-order-type">Order Type:</label>
              <select
                class="tg-select"
                wire:model.live="orderType"
                id="sales-analytics-order-type"
                >
                <option value="">All Order Types</option>
                <option value="one_time">One-time Purchase</option>
                <option value="subscription">Subscription</option>
              </select>
            </div>
          </div>
        </div>
        @if($rows->isEmpty())
          <div class="py-6 text-center text-gray">There are no sales in this period.</div>
        @else
          <table class="table text-sm md:text-base">
              <thead>
                <tr class="">
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Order #</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product Name</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Status</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4 text-center">Gross Revenue</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4 text-center">Commissions</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4 text-center">Net Earnings</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rows as $row)
                  <tr>
                    <td class="!border-b-gray/15 !py-4 !text-gray">{{ $row['date']?->format('m.d.Y H:i') ?? '—' }}</td>
                    <td class="!border-b-gray/15 !py-4 !text-gray">{{ $row['order_id'] ? '#'.$row['order_id'] : '—' }}</td>
                    <td class="!border-b-gray/15 !py-4 text-nowrap">
                      @if($row['product'])
                        <x-link :href="$row['product']->makeUrl()" :border="false">{{ $row['product']->title }}</x-link>
                      @else
                        <span class="text-gray">Product removed</span>
                      @endif
                    </td>
                    <td class="!border-b-gray/15 !py-4 text-nowrap">{{ $row['status'] }}</td>
                    <td class="!border-b-gray/15 !py-4 text-center">{{ currency($row['gross']) }}</td>
                    <td class="!border-b-gray/15 !py-4 text-center">{{ currency($row['commissions']) }}</td>
                    <td class="!border-b-gray/15 !py-4 text-center">{{ currency($row['net']) }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot></tfoot>
            </table>
        @endif
      </div>
    </x-card>
</div>
