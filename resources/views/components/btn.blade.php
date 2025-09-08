@props([
  'outlined' => false,
])
<a 
  class="main-btn py-2.5 {{ $attributes->get('class') }} {{ $outlined ? 'main-btn-outlined' : '' }}" 
  href="{{ $attributes->get('href') ?? '#' }}"
  {{ $attributes }}
>
  {{ $slot }}
</a>