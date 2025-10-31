<div>
    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Referral Program Summary</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Total Referrals:</div>
            <div class="text-nowrap">{{ number_format($summary['total_referrals']) }}</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Active Referrals:</div>
            <div class="text-nowrap">{{ number_format($summary['active_referrals']) }}</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Commission Earned:</div>
            <div class="text-nowrap">{{ currency($summary['referral_income']) }}</div>
          </div>
        </div>
      </div>
    </div>

    @if($rows->isEmpty())
      <div class="text-lg text-center text-gray">You haven't referred any users yet.</div>
    @else
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
        <table class="min-w-full bg-inherit">
          <thead>
            <tr>
              <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">Referred User</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">Referral Date</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">Referral Type</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">Status</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">Promo Codes</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">Commission Earned</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $row)
              @php
                $user = $row['user'];
                $registeredAt = $row['registered_at'];
                $formattedDate = $registeredAt
                  ? $registeredAt->copy()->timezone(config('app.timezone'))->format('m.d.Y')
                  : '—';
                $statusLabel = $row['is_active'] ? 'Active' : 'Registered';
                $promoCodes = $row['promo_codes'];
              @endphp
              <tr class="[&_td:first-child]:!rounded-tl-xl [&_td:last-child]:!rounded-tr-xl [&_td:first-child]:!rounded-bl-xl [&_td:last-child]:!rounded-br-xl border-y-[20px]">
                <td class="!border-none !bg-white !bg-clip-content !py-1 !rounded-tl-2xl !rounded-bl-2xl">
                  <div class="!px-4 !py-6 text-nowrap">
                    {{ $user?->username ?? $user?->name ?? '—' }}
                  </div>
                </td>
                <td class="!border-none !bg-white !bg-clip-content !py-1 text-nowrap !text-gray">
                  <div class="!px-4 !py-6">
                    {{ $formattedDate }}
                  </div>
                </td>
                <td class="!border-none !bg-white !bg-clip-content !py-1">
                  <div class="!px-4 !py-6">
                    {{ $row['type'] ?? '—' }}
                  </div>
                </td>
                <td class="!border-none !bg-white !bg-clip-content !py-1 text-nowrap">
                  <div class="!px-4 !py-6">
                    {{ $statusLabel }}
                  </div>
                </td>
                <td class="!border-none !bg-white !bg-clip-content !py-1">
                  <div class="!px-4 !py-6 flex flex-col gap-1">
                    @forelse($promoCodes as $code)
                      <span class="text-sm">{{ $code }}</span>
                    @empty
                      <span class="text-gray">—</span>
                    @endforelse
                  </div>
                </td>
                <td class="!border-none !bg-white !bg-clip-content !py-1 text-nowrap !rounded-tr-2xl !rounded-br-2xl">
                  <div class="!px-4 !py-6">
                    {{ currency($row['commission']) }}
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot></tfoot>
        </table>
      </div>

      @if($hasMore)
        <div class="text-right">
          <x-btn href="{{ route('profile.referal') }}" outlined class="!border-active hover:!border-second !w-auto !px-12">
            View All Referrals
          </x-btn>
        </div>
      @endif
    @endif
</div>
