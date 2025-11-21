<div class="!p-2 sm:!p-4 lg:!p-8 bg-light basis-1/2 rounded flex flex-col justify-start items-start gap-2">
  <div class="font-bold text-lg mb-2">Lifetime Payout Summary</div>
  
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Total Withdrawn:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ currency($stats['total_withdrawn'] ?? 0) }}</span>
      <x-tooltip message="The total amount of money you have successfully withdrawn from the platform across all time.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Total Payouts (Count):</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ number_format($stats['total_payouts_count'] ?? 0) }}</span>
      <x-tooltip message="The total number of individual payout transactions you have completed.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Average Payout Amount:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ currency($stats['average_payout_amount'] ?? 0) }}</span>
      <x-tooltip message="The average amount of a single payout transaction, calculated as Total Withdrawn / Total Payouts.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  
  @if(isset($stats['next_scheduled_payout']) && $stats['next_scheduled_payout'])
    <div class="flex justify-start items-center gap-1.5 flex-wrap">
      <div class="text-gray">Next Scheduled Payout:</div>
      <div class="text-nowrap relative !pr-6">
        <span>{{ $stats['next_scheduled_payout']['date'] ?? '—' }} - {{ currency($stats['next_scheduled_payout']['amount'] ?? 0) }}</span>
        <x-tooltip message="The date and estimated amount of your next automatic payout, based on your settings.">@include('icons.shield')</x-tooltip>
      </div>
    </div>
  @endif
</div>

