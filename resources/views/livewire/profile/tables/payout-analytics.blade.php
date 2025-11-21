<div id="payout-analytics">
  <x-card size="sm">
    <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
      {{-- Filters --}}
      <div class="flex justify-start items-start xl:items-center flex-col xl:flex-row !gap-4 xl:!gap-8 !mb-6">
        <div class="flex justify-start items-start sm:items-center !gap-4 2xl:!gap-8 flex-col sm:flex-row">
          <div class="block">
            <label class="text-gray mb-1 block" for="payout-status-filter">Payout Status:</label>
            <select
              id="payout-status-filter"
              wire:model.live="statusFilter"
              class="tg-select"
            >
              <option value="all">All Statuses</option>
              <option value="{{ \App\Models\Payout::STATUS_PROCESSING }}">Processing</option>
              <option value="{{ \App\Models\Payout::STATUS_IN_TRANSIT }}">In Transit</option>
              <option value="{{ \App\Models\Payout::STATUS_PAID }}">Paid</option>
              <option value="{{ \App\Models\Payout::STATUS_FAILED }}">Failed</option>
              <option value="{{ \App\Models\Payout::STATUS_CANCELED }}">Canceled</option>
            </select>
          </div>
          
          <div class="block">
            <label class="text-gray mb-1 block" for="payout-method-filter">Payout Method:</label>
            <select
              id="payout-method-filter"
              wire:model.live="methodFilter"
              class="tg-select"
            >
              <option value="all">All Methods</option>
              @foreach($payoutMethods as $method)
                <option value="{{ $method['id'] }}">{{ $method['label'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      @if($payouts->isEmpty())
        <div class="py-6 text-center text-gray">You haven't made any payout requests yet.</div>
      @else
        <div class="font-bold text-lg px-1 mb-4">Payouts</div>
        <table class="table text-sm md:text-base">
        <thead>
          <tr class="">
            <th 
              wire:click="sortBy('created_at')" 
              class="text-nowrap font-normal !border-b-gray/15 !pb-4 cursor-pointer hover:text-active transition"
            >
              Date
              @if($sortBy === 'created_at')
                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
              @endif
            </th>
            <th 
              wire:click="sortBy('payout_id')" 
              class="text-nowrap font-normal !border-b-gray/15 !pb-4 cursor-pointer hover:text-active transition"
            >
              Payout ID
              @if($sortBy === 'payout_id')
                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
              @endif
            </th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Payout Method</th>
            <th 
              wire:click="sortBy('status')" 
              class="text-nowrap font-normal !border-b-gray/15 !pb-4 cursor-pointer hover:text-active transition"
            >
              Status
              @if($sortBy === 'status')
                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
              @endif
            </th>
            <th 
              wire:click="sortBy('amount')" 
              class="text-nowrap font-normal !border-b-gray/15 !pb-4 cursor-pointer hover:text-active transition"
            >
              Amount
              @if($sortBy === 'amount')
                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
              @endif
            </th>
            <th 
              wire:click="sortBy('fees')" 
              class="text-nowrap font-normal !border-b-gray/15 !pb-4 cursor-pointer hover:text-active transition"
            >
              Fees
              @if($sortBy === 'fees')
                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
              @endif
            </th>
            <th 
              wire:click="sortBy('total_deducted')" 
              class="text-nowrap font-normal !border-b-gray/15 !pb-4 cursor-pointer hover:text-active transition"
            >
              Total Deducted
              @if($sortBy === 'total_deducted')
                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
              @endif
            </th>
          </tr>
        </thead>
        <tbody>
          @foreach($payouts as $payout)
            @php
              $date = $payout->created_at;
              $formattedDate = $date
                ? $date->copy()->timezone(config('app.timezone'))->format('m.d.Y H:i')
                : '—';
              
              $statusColors = [
                \App\Models\Payout::STATUS_PENDING => 'text-yellow-600',
                \App\Models\Payout::STATUS_PROCESSING => 'text-blue-600',
                \App\Models\Payout::STATUS_IN_TRANSIT => 'text-blue-500',
                \App\Models\Payout::STATUS_PAID => 'text-green-600',
                \App\Models\Payout::STATUS_COMPLETED => 'text-green-600',
                \App\Models\Payout::STATUS_REJECTED => 'text-red-600',
                \App\Models\Payout::STATUS_FAILED => 'text-red-600',
                \App\Models\Payout::STATUS_CANCELED => 'text-gray-600',
              ];
              $statusLabels = [
                \App\Models\Payout::STATUS_PENDING => 'Pending',
                \App\Models\Payout::STATUS_PROCESSING => 'Processing',
                \App\Models\Payout::STATUS_IN_TRANSIT => 'In Transit',
                \App\Models\Payout::STATUS_PAID => 'Paid',
                \App\Models\Payout::STATUS_COMPLETED => 'Completed',
                \App\Models\Payout::STATUS_REJECTED => 'Rejected',
                \App\Models\Payout::STATUS_FAILED => 'Failed',
                \App\Models\Payout::STATUS_CANCELED => 'Canceled',
              ];
              $statusColor = $statusColors[$payout->status] ?? 'text-gray';
              $statusLabel = $statusLabels[$payout->status] ?? ucfirst($payout->status);
              
              $payoutMethodDisplay = $payout->payout_method_display ?? '—';
              $payoutId = $payout->payout_id ?? ('POUT-' . str_pad($payout->id, 8, '0', STR_PAD_LEFT));
              $fees = $payout->fees ?? 0;
              $totalDeducted = $payout->total_deducted ?? ($payout->amount + $fees);
            @endphp
            <tr>
              <td class="!border-b-gray/15 !py-4 text-nowrap">{{ $formattedDate }}</td>
              <td class="!border-b-gray/15 !py-4 text-nowrap">
                <button
                  wire:click="openPayoutDetails({{ $payout->id }})"
                  class="text-active hover:underline cursor-pointer"
                >
                  {{ $payoutId }}
                </button>
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">
                {{ $payoutMethodDisplay }}
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap">
                <span class="{{ $statusColor }} font-semibold">{{ $statusLabel }}</span>
                @if($payout->failure_message || $payout->rejection_reason)
                  <x-tooltip message="{{ $payout->failure_message ?? $payout->rejection_reason }}">
                    <span class="ml-1 text-red-500">⚠</span>
                  </x-tooltip>
                @endif
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">
                -{{ currency(abs($payout->amount)) }} {{ strtoupper($payout->currency ?? 'USD') }}
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">
                {{ $fees > 0 ? currency($fees) : '—' }} {{ $fees > 0 ? strtoupper($payout->currency ?? 'USD') : '' }}
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">
                -{{ currency(abs($totalDeducted)) }} {{ strtoupper($payout->currency ?? 'USD') }}
              </td>
            </tr>
          @endforeach
        </tbody>
        </table>
      @endif
    </div>
  </x-card>
</div>

