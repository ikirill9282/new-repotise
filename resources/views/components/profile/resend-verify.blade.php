@php
  $class = $attributes->get('class') ?? '';   
@endphp

<x-card size="sm" class="border-1 border-active !rounded-2xl {{ $class }}">
  <div class="flex flex-col justify-start items-start gap-3">
      <div class="">
        <div class="font-semibold text-2xl mb-3">Action Required: Re-Submit Verification</div>
        <div class="">There was an issue with your verification, or a re-review is needed. Please resubmit your information via Stripe to ensure payout access. Need help? Visit our Help Center.</div>
      </div>
      <div class="flex flex-col sm:flex-row justify-start items-center !gap-2 sm:!gap-3 w-full sm:w-auto">
        <x-btn outlined class="text-nowrap" href="{{ route('help-center') }}">Help Center</x-btn>
        <x-btn class="text-nowrap !px-16" href="{{ route('verify') }}">Start Verification</x-btn>
      </div>
    </div>
</x-card>