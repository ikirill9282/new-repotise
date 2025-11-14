<div>
  <x-profile.wrap>
    <x-card size="sm" class="mb-4">
      <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
        <div class="font-bold text-2xl">Sales Analytics</div>
        <div class="block">
          <label class="text-gray" for="analytics-period">Time period:</label>
          <select
            id="analytics-period"
            wire:model.live="period"
            class="outline-0 pr-1 hover:cursor-pointer"
            >
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="60">Last 60 days</option>
          </select>
        </div>
      </div>

      <div class="flex justify-between items-stretch gap-3 flex-col md:flex-row text-sm sm:text-base">
        <div class="!p-2 sm:!p-4 lg:!p-8 bg-light basis-1/2 rounded flex flex-col justify-start items-start gap-2">
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Total Revenue:</div>
            <div class="text-nowrap relative !pr-6">
              <span>{{ currency($summary['total_revenue'] ?? 0) }}</span>
              <x-tooltip message="Total revenue earned from all sources (product sales, donations, etc.) within the selected time period.">@include('icons.shield')</x-tooltip>
            </div>
          </div>
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Product Revenue:</div>
            <div class="text-nowrap relative !pr-6">
              <span>{{ currency($summary['product_revenue'] ?? 0) }}</span>
              <x-tooltip message="Total revenue specifically from product sales within the selected time period.">@include('icons.shield')</x-tooltip>
            </div>
          </div>
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Donations Revenue:</div>
            <div class="text-nowrap relative !pr-6">
              <span>{{ currency($summary['donation_revenue'] ?? 0) }}</span>
              <x-tooltip message="Total revenue received from donations within the selected time period.">@include('icons.shield')</x-tooltip>
            </div>
          </div>
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Insights Views:</div>
            <div class="text-nowrap relative !pr-6">
              <span>{{ number_format($summary['insights_views'] ?? 0) }}</span>
              <x-tooltip message="Total views of all articles published by you within the selected time period.">@include('icons.shield')</x-tooltip>
            </div>
          </div>
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Creator Page Views:</div>
            <div class="text-nowrap relative !pr-6">
              <span>{{ number_format($summary['creator_page_views'] ?? 0) }}</span>
              <x-tooltip message="Total views of your Creator Page within the selected time period.">@include('icons.shield')</x-tooltip>
            </div>
          </div>
        </div>
        
        @if($table == 'sales-analytics')
          <x-profile.analytics.sales :stats="$salesStats"></x-profile.analytics.sales>
        @elseif($table == 'product-analytics')
          <x-profile.analytics.product :stats="$productStats"></x-profile.analytics.product>
        @elseif($table == 'article-analytics')
          <x-profile.analytics.article :stats="$articleStats"></x-profile.analytics.article>
        @elseif($table == 'donation-analytics')
          <x-profile.analytics.donation :stats="$donationStats"></x-profile.analytics.donation>
        @endif
      </div>
    </x-card>

    @livewire('profile.tables', [
      'tables' => [
        [
          'name' => 'sales-analytics',
          'title' => 'Sales',
        ],
        [
          'name' => 'product-analytics',
          'title' => 'Products',
        ],
        [
          'name' => 'article-analytics',
          'title' => 'Insights',
        ],
        [
          'name' => 'donation-analytics',
          'title' => 'Donations',
        ],
      ],
      'active' => $table ?? 'sales-analytics',
      'args' => ['period' => $period],
    ])
  </x-profile.wrap>
</div>
