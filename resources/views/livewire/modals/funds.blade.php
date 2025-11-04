<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold mb-2">Add Funds to Your Balance</div>
  <div class="text-sm text-gray pb-6 mb-4 border-b-1 border-gray/30">
    Check this box to add the payment processing fee to your transaction amount. This ensures the full amount you entered above is added to your balance. If unchecked, the fee will be deducted from the amount you add.
  </div>

  {{-- FORM --}}
  <form class="flex flex-col gap-4 pb-6 mb-4 border-b-1 border-gray/30">
    <x-form.input 
      type="number"
      step="0.01"
      min="0"
      label="Amount"
      placeholder="0.00"
      :tooltip="false"
      inputClass="!text-base"
      wire:model.live="amount"
    />

    <x-form.checkbox 
      wire:model.live="coverFees"
      :tooltip="false"
      label="Cover Processing Fees ({{ rtrim(rtrim(number_format($processingPercent, 2, '.', ''), '0'), '.') }}% + ${{ number_format($processingFlat, 2) }})"
    />

    {{-- PAYMENT METHODS TITLE --}}
    <div class="text-2xl font-semibold mt-2">
      Choose Payment Method
    </div>

    {{-- PAYMENT METHODS --}}
    @if(!empty($paymentMethods))
      <div class="flex flex-col justify-start items-start gap-2.5 group">
        @foreach($paymentMethods as $method)
          <x-form.payment-method
            wire:model="selectedPaymentMethod"
            name="funds_payment_method" 
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
        <span>No payment methods saved yet.</span>
        <x-btn second class="!inline-block !text-sm !px-3 !py-1 w-auto">+ Add Payment Method</x-btn>
      </div>
    @endif
  </form>

  {{-- TOTAL --}}
  <div class="w-full flex flex-col items-stretch gap-2 mb-4">
    <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
      <div class="">Amount to add:</div>
      <div class="text-dark font-semibold">{{ currency($summary['credited']) }}</div>
    </div>
    <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
      <div class="">Processing Fee:</div>
      <div class="text-dark font-semibold">{{ currency($summary['processing_fee']) }}</div>
    </div>
    <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
      <div class="">Total Charge:</div>
      <div class="text-dark font-semibold">{{ currency($summary['total_charge']) }}</div>
    </div>
    <div class="text-xs text-gray">
      @if($coverFees)
        {{ currency($summary['processing_fee']) }} will be charged on top of your amount so {{ currency($summary['amount']) }} is credited to your balance.
      @else
        {{ currency($summary['processing_fee']) }} will be deducted from the amount you add, so {{ currency($summary['credited']) }} is credited to your balance.
      @endif
    </div>
  </div>

  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3 max-w-xl mx-auto">
    <x-btn class="!text-sm sm:!text-base !w-auto !px-6" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!text-sm sm:!text-base !grow">Add Funds</x-btn>
  </div>
</div>
