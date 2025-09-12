<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2 max-w-xs mx-auto sm:max-w-none">Payment Initiated Successfully</div>

    {{-- LOGO --}}
    <div class="!mb-2">
      @include('icons.success')
    </div>

    {{-- TEXT --}}
    <div class="mb-4">
      <p>
        Your payment was successful! The funds should now be reflected in your available balance.
      </p>
    </div>

    {{-- BUTTON --}}
    <div class="mx-auto max-w-2xs">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="uppercase">Done</x-btn>
    </div>
</div>