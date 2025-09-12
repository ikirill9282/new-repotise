<div>
    {{-- HEADER --}}
    <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Withdraw Funds</div>

    {{-- FORM --}}
    <form class="pb-6 mb-4 border-b-1 border-gray/30" action="">

        {{-- INPUTS --}}
        <div class="mb-5">
          <x-form.input data-input="integer" label="Amount to Withdraw" placeholder="$50" class="mb-3" />
          <x-form.checkbox label="Cover Processing Fees (2,9%+0.3$)" tooltip="true" />
        </div>

        {{-- PAYOUT METHODS --}}
        <div class="mb-5">
          <div class="text-2xl font-semibold">Select Payout Method</div>
          <div class="flex flex-col justify-start items-start gap-2.5 group">
              <x-form.payment-method name="payment_method" value="payment_method_1" :tooltip="false" :editor="false"
                  :icons="true" class="bg-transparent group-has-[input]:!px-0" />

              <x-form.payment-method name="payment_method" value="payment_method_2" :tooltip="false" :editor="false"
                  :icons="true" class="bg-transparent group-has-[input]:!px-0" />

              <div class="">
                  <x-btn second class="!inline-block !text-sm !px-3 !py-1">+ Add New Payout Method</x-btn>
              </div>
          </div>
        </div>

        {{-- PAYOUT SPEED --}}
        <div class="text-2xl font-semibold mb-2">Select Withdrawal Speed</div>
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 text-gray text-sm">
            <label for="radio1" class="hover:cursor-pointer group/radio">
                <input name="speed" type="radio" id="radio1" class="absolute !w-0 !h-0 !opacity-0">
                <div class="mb-1">Regular Withdrawal</div>
                <div class="bg-light flex items-center justify-between py-2 px-3 rounded gap-3">
                    <div class="grow">
                        <p>Fee: 0.25% + $0.25</p>
                        <p>Estimated arrival: 2-4 business days.</p>
                    </div>
                    <div class="">
                      <div class="!w-4 !h-4 rounded-sm transition flex justify-center items-center
                        border !border-gray group-hover/radio:!border-active 
                        group-has-checked/radio:bg-active group-has-checked/radio:!border-active
                        group-has-checked/radio:text-white
                        ">
                        <span class="hidden  group-has-checked/radio:inline-block">
                          @include('icons.check')
                        </span>
                      </div>
                    </div>
                </div>
            </label>

            <label for="radio2" class="hover:cursor-pointer group/radio">
                <input name="speed" type="radio" id="radio2" class="absolute !w-0 !h-0 !opacity-0">
                <div class="mb-1">Express Withdrawal</div>
                <div class="bg-light flex items-center justify-between py-2 px-3 rounded gap-3">
                    <div class="grow">
                        <p>Fee: 1%</p>
                        <p>Estimated arrival: ~30 minutes.</p>
                    </div>
                    <div class="">
                      <div class="!w-4 !h-4 rounded-sm transition flex justify-center items-center
                        border !border-gray group-hover/radio:!border-active 
                        group-has-checked/radio:bg-active group-has-checked/radio:!border-active
                        group-has-checked/radio:text-white
                        ">
                        <span class="hidden  group-has-checked/radio:inline-block">
                          @include('icons.check')
                        </span>
                      </div>
                    </div>
                </div>
            </label>
        </div>
    </form>

    {{-- TOTAL --}}
    <div class="w-full flex flex-col items-stretch gap-2 mb-4">
        <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
            <div class="">Withdrawal Amount:</div>
            <div class="">$5200</div>
        </div>
        <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
            <div class="">Selected Fee:</div>
            <div class="">$200</div>
        </div>
        <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
            <div class="">You Will Receive:</div>
            <div class="">$5200</div>
        </div>
    </div>

    {{-- TWO FACTOR --}}
    <div class="mb-4">
      <x-form.input label="Two-Factor Authentication Code" placeholder="Enter 6-digit code" :tooltip="false" inputClass="!text-base" />
    </div>

    {{-- BUTTONS --}}
    <div class="flex justify-center items-center gap-3 max-w-xl mx-auto">
        <x-btn class="!text-sm sm:!text-base !w-auto !px-6" wire:click.prevent="$dispatch('closeModal')"
            outlined>Cancel</x-btn>
        <x-btn class="!text-sm sm:!text-base !grow">Recharge Balance</x-btn>
    </div>

</div>
