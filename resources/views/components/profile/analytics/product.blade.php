
<div class="!p-2 sm:!p-4 lg:!p-8 bg-light basis-1/2 rounded flex flex-col justify-start items-start gap-2">
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Product Page Views::</div>
    <div class="text-nowrap relative !pr-6">
      <span>200 000</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Average Rating:</div>
    <div class="text-nowrap relative !pr-6">
      <div class="flex justify-start items-center h-full">
        <span class="text-yellow">@include('icons.star')</span>
        <span>4,9</span>
      </div>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-start gap-1.5 flex-wrap md:!flex-nowrap">
    <div class="text-gray md:text-nowrap">Top Selling Product:</div>
    <div class=" relative !pr-6 group">
      <x-link class="group-has-[a]:!text-active" :border="false">A Guide to Getting to Know North Korea</x-link>
      <x-tooltip class="!top-1 !translate-y-[0]" message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Average Order Value:</div>
    <div class="text-nowrap relative !pr-6">
      <span>20 000</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Product Conversion Rate:</div>
    <div class="text-nowrap relative !pr-6">
      <span>20 000</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Referral Income Earned:</div>
    <div class="text-nowrap relative !pr-6">
      <span>$3 000</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip></div>
  </div>
</div>