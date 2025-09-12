<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold mb-4">Add Funds to Your Balance</div>
  
  {{-- DESCRIPTION --}}
  <div class="pb-6 mb-4 border-b-1 border-gray/30">
    Check this box to add the payment processing fee to your transaction amount. This ensures the full amount you entered above is added to your balance. If unchecked, the fee will be deducted from the amount you add.
  </div>

  {{-- FORM --}}
  <form class="flex flex-col gap-4 pb-6 mb-4 border-b-1 border-gray/30" action="">
    <x-form.input 
      data-input="integer" 
      label="Add funds"
      placeholder="$50"
    />
    <x-form.checkbox 
      label="Cover Processing Fees (2,9%+0.3$)"
      tooltip="true"
    />

    {{-- PAYMENT METHODS TITLE --}}
    <div class="text-2xl font-semibold">
      Choose Payment Method
    </div>

    {{-- PAYMENT METHODS --}}
    <div class="flex flex-col justify-start items-start gap-2.5 group">
      <x-form.payment-method
        name="payment_method" 
        value="payment_method_1"
        :tooltip="false"
        :editor="false"
        :icons="true"
        class="bg-transparent group-has-[input]:!px-0"
      />

      <x-form.payment-method
        name="payment_method" 
        value="payment_method_2"
        :tooltip="false"
        :editor="false"
        :icons="true"
        class="bg-transparent group-has-[input]:!px-0"
      />
    </div>
  </form>

  {{-- TOTAL --}}
  <div class="w-full flex flex-col items-stretch gap-2 mb-4">
    <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
      <div class="">Amount to add:</div>
      <div class="">$5200</div>
    </div>
    <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
      <div class="">Processing Fee:</div>
      <div class="">$200</div>
    </div>
    <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
      <div class="">Total Charge:</div>
      <div class="">$5200</div>
    </div>
  </div>


  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3 max-w-xl mx-auto">
    <x-btn class="!text-sm sm:!text-base !w-auto !px-6" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!text-sm sm:!text-base !grow" >Recharge Balance</x-btn>
  </div>

</div>
