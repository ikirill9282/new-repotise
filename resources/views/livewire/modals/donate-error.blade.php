<div class="text-center space-y-6">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Payment Failed</div>

    {{-- LOGO --}}
    <div class="!mb-2">
      @include('icons.error')
    </div>

    {{-- TEXT --}}
    <div class="mb-4 max-w-lg mx-auto">
      <p class="text-gray mb-2">
        We couldn't process your payment.
      </p>
      @if(!empty($message))
        <p class="font-semibold text-dark mb-2">
          {{ $message }}
        </p>
      @endif
      <p class="text-sm text-gray">
        Please check your payment information and try again, or use a different payment method.
      </p>
      @if(!empty($errorCode))
        <p class="text-xs text-gray mt-2">
          Error code: {{ $errorCode }}
        </p>
      @endif
    </div>

    {{-- BUTTONS --}}
    <div class="max-w-xs mx-auto flex justify-center items-center gap-3">
      <x-btn 
        wire:click.prevent="$dispatch('closeModal')" 
        class="!py-1.5 !px-6" 
        outlined
      >
        Close
      </x-btn>
      <x-btn 
        wire:click.prevent="$dispatch('closeModal')"
        x-on:click.prevent="setTimeout(() => Livewire.dispatch('openModal', { modalName: 'donate', args: { seller_id: {{ $seller_id ?? request()->query('seller_id', 0) }} } }), 300)"
        class="!py-1.5 !px-6"
      >
        Try Again
      </x-btn>
    </div>
</div>
