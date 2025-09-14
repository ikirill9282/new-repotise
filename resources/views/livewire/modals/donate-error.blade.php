<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Payment Failed</div>

    {{-- LOGO --}}
    <div class="!mb-2">
      @include('icons.error')
    </div>

    {{-- TEXT --}}
    <div class="mb-4 group">
      We couldn't process your payment. <b>[Динамическое сообщение об ошибке от Stripe, например: 'Your card was declined.' или 'Invalid card details.'].</b> Please check your payment information and try again, or use a different payment method.
    </div>

    {{-- BUTTON --}}
    <div class="max-w-xs mx-auto flex justify-center items-center gap-3">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="!py-1.5" outlined>Cancel</x-btn>
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="!py-1.5">Try Again</x-btn>
    </div>
</div>
