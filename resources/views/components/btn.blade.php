@props([
  'outlined' => false,
])
<a 
  class="main-btn {{ $attributes->get('class') }} {{ $outlined ? 'main-btn-outlined' : '' }}" 
  href="{{ $attributes->get('href') ?? '#' }}"
  {{ $attributes }}
>
  {{ $slot }}
</a>