@php
  $formatter = function (float $value): string {
    if (function_exists('currency')) {
      return currency($value);
    }

    return '$' . number_format($value, 2);
  };
@endphp

<div class="text-center space-y-6">

  {{-- HEADER --}}
  <div class="space-y-3">
    <div class="text-2xl font-semibold">Thank you!</div>
    <div class="flex justify-center">
      @include('icons.success')
    </div>
    <p class="max-w-lg mx-auto text-sm text-gray">
      {{ $monthlySupport
        ? "You've started monthly support for {$sellerName}. We'll send you a reminder before each renewal."
        : "Your donation to {$sellerName} has been sent successfully." }}
    </p>
  </div>

  {{-- SUMMARY --}}
  <div class="bg-light border border-gray/20 rounded-xl p-5 text-left space-y-3">
    <div class="flex items-center justify-between">
      <span class="text-sm text-gray">Donation amount</span>
      <span class="font-semibold">{{ $formatter($amount) }}</span>
    </div>
    <div class="flex items-center justify-between">
      <span class="text-sm text-gray">Total charged</span>
      <span class="font-semibold">{{ $formatter($chargedAmount) }}</span>
    </div>
    <div class="flex items-center justify-between">
      <span class="text-sm text-gray">Creator receives</span>
      <span class="font-semibold text-active">{{ $formatter($sellerAmount) }}</span>
    </div>
    <div class="flex items-center justify-between text-xs text-gray">
      <span>Platform fee</span>
      <span>{{ $formatter($platformFee) }}</span>
    </div>
    <div class="flex items-center justify-between text-xs text-gray">
      <span>Estimated Stripe fee</span>
      <span>{{ $formatter($stripeFee) }}</span>
    </div>
    @if($coverFees)
      <div class="text-xs text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
        You covered the processing fees — {{ $sellerName }} receives the full donation.
      </div>
    @endif
    @if($anonymous)
      <div class="text-xs text-gray bg-white border border-gray/10 rounded-lg px-3 py-2">
        Donation sent anonymously. The creator won’t see your name or email.
      </div>
    @endif
  </div>

  {{-- MESSAGE --}}
  @if(!empty($message))
    <div class="bg-white border border-gray/10 rounded-xl p-4 text-left space-y-2">
      <div class="text-xs uppercase tracking-wide text-gray">Your message to {{ $sellerName }}</div>
      <p class="text-sm text-dark leading-relaxed">{{ $message }}</p>
    </div>
  @endif

  {{-- CTA --}}
  <div class="space-y-3">
    <x-btn wire:click.prevent="$dispatch('closeModal')" class="!w-full sm:!w-auto !px-8">
      Done
    </x-btn>
    <div class="text-xs text-gray">
      Need to update your payment method? Visit <x-link href="{{ route('profile.checkout') }}" class="!border-none !text-sm">My Purchases</x-link>.
    </div>
  </div>
</div>
