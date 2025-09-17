<div>
  <x-profile.wrap>
    <x-card size="sm" class="mb-4">
      <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
        <div class="font-bold text-2xl">Sales Analytics</div>
        <div class="block">
          <label class="text-gray" for="sorting-reviews">Time period:</label>
          <select
            id="sorting-reviews"
            class="outline-0 pr-1 hover:cursor-pointer"
            >
            <option value="">Last 7 days</option>
            <option value="">Last 30 days</option>
            <option value="">Last 60 days</option>
          </select>
        </div>
      </div>

      <div class="flex justify-between items-stretch gap-3 flex-col md:flex-row text-sm sm:text-base">
        <div class="!p-2 sm:!p-4 lg:!p-8 bg-light basis-1/2 rounded flex flex-col justify-start items-start gap-2">
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Total Revenue:</div>
            <div class="text-nowrap relative !pr-6">
              <span>$30 000</span>
              <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
            </div>
          </div>
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Product Revenue:</div>
            <div class="text-nowrap relative !pr-6">
              <span>$30 000</span>
              <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
            </div>
          </div>
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Donations Revenue:</div>
            <div class="text-nowrap relative !pr-6">
              <span>$30 000</span>
              <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
            </div>
          </div>
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Insights Views:</div>
            <div class="text-nowrap relative !pr-6">
              <span>$30 000</span>
              <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
            </div>
          </div>
          <div class="flex justify-start items-center gap-1.5">
            <div class="text-gray">Creator Page Views:</div>
            <div class="text-nowrap relative !pr-6">
              <span>20 000 000</span>
              <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
            </div>
          </div>
        </div>
        
        @if($table == 'sales-analytics')
          <x-profile.analytics.sales></x-profile.analytics.sales>
        @elseif($table == 'product-analytics')
          <x-profile.analytics.product></x-profile.analytics.product>
        @elseif($table == 'article-analytics')
          <x-profile.analytics.article></x-profile.analytics.article>
        @elseif($table == 'donation-analytics')
          <x-profile.analytics.donation></x-profile.analytics.donation>
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
      'active' => $table,
    ])
  </x-profile.wrap>
</div>
