<div>
  <div class="text-center">
    {{-- HEADER --}}
    <div class="text-2xl font-semibold mb-6">Article Deleted</div>

    {{-- LOGO --}}
    <div class="!mb-6 flex justify-center items-center">
      @include('icons.check')
    </div>

    {{-- TEXT --}}
    <div class="mb-6 flex flex-col gap-2">
      <p>The article has been successfully deleted from the platform.</p>
    </div>

    {{-- BUTTONS --}}
    <div class="flex justify-center items-center gap-2 flex-col sm:flex-row">
      <x-btn class="!text-sm sm:!text-base" wire:click.prevent="$dispatch('closeModal')">Close</x-btn>
    </div>
  </div>
</div>

