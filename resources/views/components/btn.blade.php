@props([
  'outlined' => false,
  'second' => false,
  'gray' => false,
  'disabled' => false,
])


<a 
  class="main-btn !p-2.5 w-full !rounded !text-sm sm:!text-base
        {{ $attributes->get('class') }} 
        {{ $outlined ? 'main-btn-outlined' : '' }} 
        {{ $second ? '!bg-second !border-second hover:!bg-active hover:!border-active' : '' }}
        {{ $gray ? '!bg-light !text-gray !border-light hover:!border-gray hover:!text-second hover:!shadow-none ' : '' }}
        {{ $disabled ? '!bg-gray !border-gray pointer-events-none' : '' }}
        " 
  href="{{ $attributes->get('href') ?? '#' }}"
  {{ $attributes }}
>
  {{ $slot }}
</a>