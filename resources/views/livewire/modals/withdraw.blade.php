<div>
    {{-- HEADER --}}
    <div class="text-2xl font-semibold pb-4 border-b-1 border-gray/30">Withdraw Funds</div>

    {{-- FORM --}}
    <form class="pb-6 mb-4 border-b-1 border-gray/30">
        {{-- INPUTS --}}
        <div class="mb-5">
          <x-form.input 
            type="number"
            step="0.01"
            min="0"
            label="Amount to Withdraw"
            placeholder="0.00"
            class="mb-3"
            :tooltip="false"
            inputClass="!text-base"
            wire:model.live="amount"
          />
          <x-form.checkbox 
            wire:model.live="coverFees"
            :tooltip="false"
            label="Cover Processing Fees ({{ rtrim(rtrim(number_format($processingPercent, 2, '.', ''), '0'), '.') }}% + ${{ number_format($processingFlat, 2) }})" 
          />
        </div>

        {{-- PAYOUT METHODS --}}
        <div class="mb-5">
          <div class="text-2xl font-semibold mb-2">Select Payout Method</div>
          @if(!empty($payoutMethods))
            <div class="flex flex-col justify-start items-start gap-2.5 group">
              @foreach($payoutMethods as $method)
                <x-form.payment-method 
                  wire:model="selectedPayoutMethod"
                  name="withdraw_payout_method"
                  :value="$method['id']"
                  :tooltip="false"
                  :editor="false"
                  :icons="false"
                  :brand="$method['label']"
                  :last4="$method['last4']"
                  class="bg-transparent group-has-[input]:!px-0"
                />
              @endforeach
            </div>
          @else
            <div class="bg-light rounded !p-4 text-gray flex flex-col gap-2">
              <span>No payout methods connected yet.</span>
              <x-btn second class="!inline-block !text-sm !px-3 !py-1 w-auto">+ Add New Payout Method</x-btn>
            </div>
          @endif
        </div>

        {{-- PAYOUT SPEED --}}
        <div class="text-2xl font-semibold mb-2">Select Withdrawal Speed</div>
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 text-gray text-sm">
          @foreach($speedOptions as $key => $config)
            @php
              $feeLabel = $config['fee_percent'] . '%';
              if (!empty($config['fee_fixed'])) {
                $feeLabel .= ' + $' . number_format($config['fee_fixed'], 2);
              }
            @endphp
            <label for="withdraw-speed-{{ $key }}" class="hover:cursor-pointer group/radio w-full">
                <input 
                  name="withdraw_speed" 
                  type="radio" 
                  id="withdraw-speed-{{ $key }}" 
                  class="absolute !w-0 !h-0 !opacity-0"
                  value="{{ $key }}"
                  wire:model.live="speed"
                >
                <div class="mb-1 text-dark font-semibold">{{ $config['label'] }}</div>
                <div class="bg-light flex items-center justify-between py-2 px-3 rounded gap-3">
                    <div class="grow">
                        <p>Fee: {{ $feeLabel }}</p>
                        <p>{{ $config['description'] }}</p>
                    </div>
                    <div>
                      <div class="!w-4 !h-4 rounded-sm transition flex justify-center items-center
                        border !border-gray group-hover/radio:!border-active 
                        group-has-checked/radio:bg-active group-has-checked/radio:!border-active
                        group-has-checked/radio:text-white
                        ">
                        <span class="hidden group-has-checked/radio:inline-block">
                          @include('icons.check')
                        </span>
                      </div>
                    </div>
                </div>
            </label>
          @endforeach
        </div>
    </form>

    {{-- TOTAL --}}
    <div class="w-full flex flex-col items-stretch gap-2 mb-4">
        <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
            <div class="">Withdrawal Amount:</div>
            <div class="text-dark font-semibold">{{ currency($summary['amount']) }}</div>
        </div>
        <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
            <div class="">Selected Fee:</div>
            <div class="text-dark font-semibold">{{ currency($summary['selected_fee']) }}</div>
        </div>
        <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
            <div class="">You Will Receive:</div>
            <div class="text-dark font-semibold">{{ currency($summary['receive']) }}</div>
        </div>
        @if($coverFees)
          <div class="text-xs text-gray">
            You will be charged {{ currency($summary['debit']) }} (including processing fees) from your referral balance.
          </div>
        @else
          <div class="text-xs text-gray">
            {{ currency($summary['selected_fee']) }} in fees will be deducted from your withdrawal amount.
          </div>
        @endif
    </div>

    {{-- TWO FACTOR --}}
    <div class="mb-4">
      <x-form.input 
        label="Two-Factor Authentication Code" 
        placeholder="Enter 6-digit code" 
        :tooltip="false" 
        inputClass="!text-base" 
      />
    </div>

    {{-- BUTTONS --}}
    <div class="flex justify-center items-center gap-3 max-w-xl mx-auto">
        <x-btn 
          class="!text-sm sm:!text-base !w-auto !px-6" 
          wire:click.prevent="$dispatch('closeModal')"
          outlined
        >
          Cancel
        </x-btn>
        <x-btn class="!text-sm sm:!text-base !grow">
          Request Withdrawal
        </x-btn>
    </div>
</div>
