<div>
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Donate to [имя продавца]</div>
  
  <div class="flex flex-col pb-6 mb-4 border-b-1 border-gray/30 !gap-4">
    <div class="">
      <x-form.input placeholder="$10" data-input="integer" />
    </div>
    <div class="">
      <x-form.checkbox label="Monthly support" />
    </div>

    <div class="">
      <x-form.textarea placeholder="Мessage..." label="Add a message (optional)"></x-form.textarea>
    </div>

    <div class="group/pm">
      <div class="text-2xl font-semibold mb-4">Stripe Elements (Card Element)</div>
      <x-form.payment-method 
        class="!bg-transparent mb-2 group-has-[label]/pm:!px-0"
        :editor="false"
        :tooltip="false"
        :icons="true"
        name="payment-method"
      ></x-form.payment-method>
      <x-form.payment-method
        name="payment-method" 
        class="!bg-transparent mb-2 group-has-[label]/pm:!px-0"
        :editor="false"
        :tooltip="false"
        :icons="true"
      ></x-form.payment-method>

      <x-btn second class="!w-auto !inline-block sm:!text-sm !py-1 !px-3">+ Add New Payment Method</x-btn>
    </div>

    <div class="">
      <x-form.checkbox label="Donate anonymously" />
    </div>

    <div class="">
      <x-form.checkbox label="Cover fees (2.9%+$0.3)" />
    </div>
  </div>


  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="!py-2 !w-auto !px-6" gray wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!py-2 !grow" >Donate Now</x-btn>
  </div>
</div>
