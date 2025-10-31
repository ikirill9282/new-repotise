<div>
  <div class="flex justify-between items-center mb-4">
    <div class="font-bold text-2xl">Refunds</div>
    <div class="block">
      <label class="text-gray" for="sorting-refunds">Sort By:</label>
      <select
        wire:model="sorting"
        id="sorting-refunds"
        class="outline-0 pr-1 hover:cursor-pointer"
      >
        <option value="newest">Newest First</option>
        <option value="oldest">Oldest First</option>
        <option value="status">Status</option>
      </select>
    </div>
  </div>

  @if($refunds->isEmpty())
    <div class="text-center text-gray py-6">
      There are no refund requests yet.
    </div>
  @else
    <div class="overflow-x-scroll scrollbar-custom mb-4">
      <table class="table text-sm md:text-base">
        <thead>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </thead>
        <tbody>
          @foreach($refunds as $refund)
            @php
              $buyer = $refund['buyer'];
              $product = $refund['product'];
              $preview = $refund['preview'];
              $reason = $refund['reason'] ?: '—';
              $timeLeft = $refund['time_left'];
              $status = ucfirst(str_replace('_', ' ', $refund['status'] ?? 'pending'));
              $createdAt = $refund['model']->created_at?->copy()->timezone(config('app.timezone'));
              $avatar = $buyer?->avatar ?? asset('assets/img/avatar.svg');
            @endphp
            <tr>
              <td class="!border-b-gray/15 !py-4 text-nowrap align-middle">
                <div class="flex justify-start items-center gap-2">
                  <div class="!w-12 !h-12 rounded-full overflow-hidden shrink-0 bg-light">
                    <img class="!w-full !h-full object-cover" src="{{ $avatar }}" alt="{{ $buyer?->username ?? 'Buyer' }}">
                  </div>
                  <div class="flex flex-col">
                    <p class="">{{ $buyer?->username ?? $buyer?->name ?? 'Anonymous' }}</p>
                    <p class="text-xs text-gray">
                      {{ $createdAt?->format('m.d.Y H:i') ?? '—' }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="align-middle min-w-2xs whitespace-normal">
                <div class="font-semibold text-sm mb-1">{{ $status }}</div>
                <div class="text-gray">{{ $reason }}</div>
              </td>
              <td class="text-nowrap align-middle">{{ $timeLeft }}</td>
              <td class="!border-b-gray/15 !py-4 ">
                <div class="flex items-center justify-start gap-2">
                  <div class="!w-14 !h-22 rounded overflow-hidden shrink-0 bg-light">
                    @if($preview)
                      <img class="w-full h-full object-cover" src="{{ $preview }}" alt="{{ $product?->title ?? 'Product' }}">
                    @else
                      <div class="w-full h-full flex items-center justify-center text-xs text-gray">
                        No Image
                      </div>
                    @endif
                  </div>
                  <div class="min-w-2xs md:min-w-auto">
                    <p>{{ $product?->title ?? 'Product removed' }}</p>
                  </div>
                </div>
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap align-middle ">
                <div class="flex flex-col justify-start items-start gap-2">
                  <x-link href="{{ route('profile.sales') }}">Approve Refund</x-link>
                  <x-link href="{{ route('profile.sales') }}">Reject Refund</x-link>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
    @if($hasMore)
      <div class="text-center">
        <x-link wire:click.prevent="loadMore">Show More</x-link>
      </div>
    @endif
  @endif
</div>
