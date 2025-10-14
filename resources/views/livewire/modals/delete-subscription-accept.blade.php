<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Subsctiption deleted!</div>

    {{-- LOGO --}}
    <div class="!mb-2">
      @include('icons.success')
    </div>

    {{-- TEXT --}}
    <div class="mb-4 group">
      Your subscription has been successfully removed. You will no longer be billed, and access to subscription benefits has ended. If you wish to resume, you will need to start a new subscription.
    </div>

    {{-- BUTTON --}}
    <div class="max-w-xs mx-auto">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="">Done</x-btn>
    </div>
</div>
