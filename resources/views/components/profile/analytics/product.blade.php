@props(['stats' => []])

<div class="!p-2 sm:!p-4 lg:!p-8 bg-light basis-1/2 rounded flex flex-col justify-start items-start gap-2">
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Product Page Views:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ number_format($stats['page_views'] ?? 0) }}</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Average Rating:</div>
    <div class="text-nowrap relative !pr-6">
      <div class="flex justify-start items-center h-full gap-1">
        <span class="text-yellow">@include('icons.star')</span>
        <span>{{ number_format($stats['average_rating'] ?? 0, 1) }}</span>
      </div>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-start gap-1.5 flex-wrap md:!flex-nowrap">
    <div class="text-gray md:text-nowrap">Top Selling Product:</div>
    <div class=" relative !pr-6 group">
      @php $topProduct = $stats['top_product'] ?? null; @endphp
      @if($topProduct)
        <x-link class="group-has-[a]:!text-active" :border="false" href="{{ $topProduct->makeUrl() }}">{{ $topProduct->title }}</x-link>
      @else
        <span class="text-gray">—</span>
      @endif
      <x-tooltip class="!top-1 !translate-y-[0]" message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Average Order Value:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ currency($stats['average_order_value'] ?? 0) }}</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Product Conversion Rate:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ number_format($stats['conversion_rate'] ?? 0, 2) }}%</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Referral Income Earned:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ currency($stats['referral_income'] ?? 0) }}</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
</div>
