<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Subscription Canceled</div>

    {{-- LOGO --}}
    <div class="!mb-2">
      @include('icons.success')
    </div>

    {{-- TEXT --}}
    <div class="mb-4">
      <p>
        Your subscription to <span class="!text-dark font-semibold text-nowrap">[Название продукта/подписки]</span> has been successfully canceled. You will not be charged again. You will continue to have access until <span class="!text-dark font-semibold text-nowrap">[Дата окончания текущего периода]</span>
      </p>
    </div>

    {{-- BUTTON --}}
    <div class="mx-auto max-w-2xs">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="uppercase">Done</x-btn>
    </div>
</div>
