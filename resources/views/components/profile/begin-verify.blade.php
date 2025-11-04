@php
  $class = $attributes->get('class') ?? '';   
@endphp

<x-card class="border-1 border-active !rounded-2xl {{ $class }}">
  <div class="flex flex-col md:flex-row justify-between items-center md:items-center gap-3">
      <div class="">
        <div class="font-semibold text-2xl">Turn Your Travel Passion into Profit!</div>
      </div>
      <div class="flex gap-2 sm:gap-4 w-full sm:w-auto">
        <x-btn class="text-nowrap !text-sm sm:!text-base !w-full sm:w-auto sm:!px-16" href="{{ route('verify') }}" outlined>Become a Creator</x-btn>
        <x-btn class="text-nowrap !text-sm sm:!text-base !w-full sm:w-auto sm:!px-16" href="{{ route('sellers') }}">Learn More</x-btn>
      </div>
    </div>
</x-card>
