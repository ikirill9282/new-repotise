<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Two-Factor Authentication Disabled</div>

    {{-- LOGO --}}
    <div class="!mb-2 flex justify-center items-center py-4">
      @include('icons.warning')
    </div>

    {{-- TEXT --}}
    <div class="mb-4">
      <p>
        Two-Factor Authentication has been disabled for your account. For better security, we strongly recommend enabling it again.
      </p>
    </div>

    {{-- BUTTON --}}
    <div class="max-w-38 mx-auto">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="uppercase">Done</x-btn>
    </div>
</div>
