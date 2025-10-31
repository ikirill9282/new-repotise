<div>
    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Donation Summary</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Donation Revenue:</div>
            <div class="text-nowrap">{{ currency($summary['donation_revenue']) }}</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Total Donations:</div>
            <div class="text-nowrap">{{ number_format($summary['total_donations']) }}</div>
          </div>
        </div>
      </div>
    </div>

    @if($rows->isEmpty())
      <div class="text-lg text-center text-gray">You haven't received any donations yet.</div>
    @else
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
        <div class="font-bold text-lg px-1 mb-4">Recent Donations</div>
        <table class="table">
          <thead>
            <tr class="">
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date & Time</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Donor Name</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Donation Amount</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Message</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $fund)
              @php
                $donor = $fund->related;
                $date = $fund->created_at;
                $formattedDate = $date
                  ? $date->copy()->timezone(config('app.timezone'))->format('m.d.Y H:i')
                  : '—';
                $donorName = $donor instanceof \App\Models\User
                  ? ($donor->username ?? $donor->name ?? null)
                  : null;
              @endphp
              <tr>
                <td class="!border-b-gray/15 !py-4 text-nowrap">{{ $formattedDate }}</td>
                <td class="!border-b-gray/15 !py-4 text-nowrap">
                  {{ $donorName ?? '—' }}
                </td>
                <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">
                  {{ currency((float) $fund->sum) }}
                </td>
                <td class="!border-b-gray/15 !py-4">
                  @if(!empty($fund->message))
                    <div class="flex items-center gap-2">
                      <div>@include('icons.message')</div>
                      <span>{{ \Illuminate\Support\Str::limit($fund->message, 80) }}</span>
                    </div>
                  @else
                    <span class="text-gray">—</span>
                  @endif
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
            View All Donations
          </x-btn>
        </div>
      @endif
    @endif
</div>
