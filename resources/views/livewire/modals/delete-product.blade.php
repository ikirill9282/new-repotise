<div>
  <div class="text-center">
    {{-- HEADER --}}
    <div class="text-2xl font-semibold mb-6">Delete Product?</div>

    {{-- LOGO --}}
    <div class="!mb-6 flex justify-center items-center">
      @include('icons.warning')
    </div>

    {{-- TEXT --}}
    <div class="mb-6 flex flex-col gap-2">
      <p>Are you sure you want to delete this product?</p>
      <p>The product will be removed from the marketplace and hidden from all users. However, customers who have already purchased this product will retain access to it.</p>
      <p class="font-bold">This action cannot be undone.<br>Do you want to proceed?</p>
    </div>

    {{-- BUTTONS --}}
    <div class="flex justify-center items-center gap-2 flex-col sm:flex-row">
      <x-btn class="!text-sm sm:!text-base" wire:click.prevent="$dispatch('closeModal')">Cancel</x-btn>
      <x-btn class="!text-sm sm:!text-base" wire:click.prevent="deleteProduct" outlined>Yes, Delete Product</x-btn>
    </div>
  </div>
</div>

