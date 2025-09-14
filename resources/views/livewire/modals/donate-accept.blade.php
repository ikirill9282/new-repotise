<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Thank you!</div>

    {{-- LOGO --}}
    <div class="!mb-2">
      @include('icons.success')
    </div>

    {{-- TEXT --}}
    <div class="mb-4">
      Your donation of <b>$[Amount]</b> to <b>[Seller Name]</b> has been successfully sent. Thank you for your support!
    </div>

    {{-- BUTTON --}}
    <div class="max-w-xs mx-auto">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="">Done</x-btn>
    </div>
</div>
