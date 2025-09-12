@props([
  'outlined' => false,
  'second' => false,
])
<a 
  class="main-btn py-2.5 
        {{ $attributes->get('class') }} 
        {{ $outlined ? 'main-btn-outlined' : '' }} 
        {{ $second ? '!bg-second !border-second hover:!bg-active hover:!border-active' : '' }}
        " 
  href="{{ $attributes->get('href') ?? '#' }}"
  {{ $attributes }}
>
  {{ $slot }}
</a>