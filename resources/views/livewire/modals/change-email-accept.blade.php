<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Withdrawal Request Submitted</div>

    {{-- LOGO --}}
    <div class="!mb-2">
      @include('icons.success')
    </div>

    {{-- TEXT --}}
    <div class="mb-4">
      <p class="mb-3">
        Your withdrawal request has been successfully submitted. We are now processing the transfer to your selected account. Processing times can vary depending on your bank.
      </p>
      <p>Funds typically arrive within 2-4 business days.</p>
    </div>

    {{-- BUTTON --}}
    <div class="max-w-40 mx-auto">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="uppercase">ok</x-btn>
    </div>
</div>
