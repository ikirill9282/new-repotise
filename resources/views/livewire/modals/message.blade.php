<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Message</div>

  {{-- MESSAGE --}}
  <div class="flex justify-between items-start !gap-4 pb-6 mb-4 border-b-1 border-gray/30">
    <div class="">
      <div class="!w-14 !h-14 sm:!w-20 sm:!h-20 !rounded-full overflow-hidden">
        <img class="object-cover w-full h-full" src="/storage/images/default_avatar.png" alt="">
      </div>
    </div>
    <div class="">
      <p class="">@talmaev1</p>
      <p class="!my-1 text-sm text-gray">01.22.2025</p>
      <p class="">Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend! Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend! Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend! Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend! Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend! Deceives, 4 hours and did not issue the order. Does not cancel! I do not recomme</p>
    </div>

  </div>
  <div class="!mb-4">
    <x-form.text-counter 
      :emoji="true"
      max="1000" 
      placeholder="Add a message..."
    ></x-form.text-counter>
  </div>


  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="w-auto m-0 !px-8 !bg-light !text-gray !border-light" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="w-auto m-0 grow" >Send</x-btn>
  </div>
</div>
