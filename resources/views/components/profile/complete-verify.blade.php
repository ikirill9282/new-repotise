@php
  $class = $attributes->get('class') ?? '';   
@endphp

<x-card size="sm" class="border-1 border-active !rounded-2xl {{ $class }}">
  <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-3">
      <div class="">
        <div class="font-semibold text-2xl mb-3">Complete Verification Required</div>
        <div class="">You've earned $100! To comply with regulations and unlock payouts, please complete the full identity verification via Stripe. Other account functions remain active.</div>
      </div>
      <div class="">
        <x-btn class="text-nowrap !px-16" href="{{ route('verify') }}">Start Verification</x-btn>
      </div>
    </div>
</x-card>