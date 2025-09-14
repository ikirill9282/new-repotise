<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Thank you!</div>

    {{-- LOGO --}}
    <div class="!mb-2">
      @include('icons.success')
    </div>

    {{-- TEXT --}}
    <div class="mb-4 group">
      You have successfully signed up for monthly support for <b>[Seller Name]</b>. The first payment <b>$[Amount]</b> has been made. You can manage your subscription at «<x-link class="!border-none group-has-[a]:!text-active">My Purchases</x-link>.»
    </div>

    {{-- BUTTON --}}
    <div class="max-w-xs mx-auto">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="">Done</x-btn>
    </div>
</div>
