
<div class="!p-2 sm:!p-4 lg:!p-8 bg-light basis-1/2 rounded flex flex-col justify-center items-start gap-2">
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Recurring Donations:</div>
    <div class="text-nowrap relative !pr-6">
      <span>200 000</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Average Donation Amount:</div>
    <div class="text-nowrap relative !pr-6">
      <span>2 000 000</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Top Donor (by Value):</div>
    <div class="text-nowrap relative !pr-6">
      <span>@talmaev1</span>
      <x-tooltip message="tooltip">@include('icons.shield')</x-tooltip>
    </div>
  </div>
</div>