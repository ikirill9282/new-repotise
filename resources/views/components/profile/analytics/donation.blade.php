@props(['stats' => []])

<div class="!p-2 sm:!p-4 lg:!p-8 bg-light basis-1/2 rounded flex flex-col justify-center items-start gap-2">
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Recurring Donations:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ number_format($stats['recurring_donations'] ?? 0) }}</span>
      <x-tooltip message="Number of active recurring donations during the selected time period.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Average Donation Amount:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ currency($stats['average_donation'] ?? 0) }}</span>
      <x-tooltip message="Average amount donated per transaction during the selected time period.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Top Donor (by Value):</div>
    <div class="text-nowrap relative !pr-6">
      @php $topDonor = $stats['top_donor'] ?? null; @endphp
      <span>{{ $topDonor?->username ?? $topDonor?->name ?? '—' }}</span>
      <x-tooltip message="Donor who contributed the highest total amount during the selected time period.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
</div>
