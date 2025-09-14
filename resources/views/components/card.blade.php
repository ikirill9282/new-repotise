@props([
  'size' => null,
])

@php
  $padding = match($size) {
    'xs' => '!p-2 xs:!p-4 lg:!p-6',
    'sm' => '!p-2 xs:!p-4 lg:!p-8',
    default => '!p-3 xs:!p-6 lg:!p-12',
  }
@endphp

<div class="bg-white !border-gray/50 rounded-lg {{ $padding }} {{ $attributes->get('class') }}">
  {{ $slot }}
</div>