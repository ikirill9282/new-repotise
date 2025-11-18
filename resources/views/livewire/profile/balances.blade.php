<x-card size="sm">
  <div class="font-semibold text-2xl mb-5">Dashboard</div>
  <div class="flex justify-between items-center mb-4">
    <div class="text-lg">Balance & Payouts</div>
    <div class="flex justify-end items-center gap-2">
      <label for="balances-date" class="text-gray">Time Period:</label>
      <select 
        class="tg-select"
        wire:model.live="period"
        id="balances-date"
      >
        @foreach($periodOptions ?? [] as $value => $label)
          <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="flex flex-col">
    <div class="group mb-4">
      <div class="text-gray mb-1 text-sm">Available Balance</div>
      <div class="px-3 py-2.5 bg-light rounded relative text-gray">
        ${{ number_format($availableBalance, 2, '.', ' ') }}
        <x-tooltip message="Funds available for immediate withdrawal to your payout method." class="!right-3" />
      </div>
    </div>
    <div class="group mb-4">
      <div class="text-gray mb-1 text-sm">Pending Balance</div>
      <div class="px-3 py-2.5 bg-light rounded relative text-gray">
        ${{ number_format($pendingBalance, 2, '.', ' ') }}
        <x-tooltip message="Earnings currently pending clearance (for example, recent sales or refund hold periods)." class="!right-3" />
      </div>
    </div>
  </div>

  <div class="flex flex-col-reverse sm:flex-row justify-center items-center !gap-2 lg:!gap-4">
    <x-btn class="grow">Withdraw Funds</x-btn>
    <x-btn outlined class="lg:!w-auto text-nowrap px-4">Top Up Balance</x-btn>
  </div>
</x-card>
