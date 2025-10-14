<div>
  <div class="text-center">
    {{-- HEADER --}}
    <div class="text-2xl font-semibold mb-6">Delete Subscription?</div>

    {{-- LOGO --}}
    <div class="!mb-6 flex justify-center items-center">
      @include('icons.warning')
    </div>

    {{-- TEXT --}}
    <div class="mb-6 flex flex-col gap-2">
      <p>This action cannot be undone. Subscription data has not yet been finalized, and deleting the subscription has no effects beyond requiring you to place a new order to restore access.</p>
      <p>You will lose access to subscription benefits and all associated data, and you will need to re-enter any details or preferences when you create a new order.</p>
      <p class="font-bold">This change is permanent and cannot be reversed.<br> Do you want to proceed?</p>
    </div>

    {{-- BUTTONS --}}
    <div class="flex justify-center items-center gap-2 flex-col sm:flex-row">
      <x-btn class="!text-sm sm:!text-base" wire:click.prevent="$dispatch('closeModal')">Keep Subscription</x-btn>
      <x-btn class="!text-sm sm:!text-base" wire:click.prevent="deleteSubscription" outlined>Yes, Delete Subscription</x-btn>
    </div>
  </div>

</div>
