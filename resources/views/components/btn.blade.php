<a 
  class="main-btn {{ $attributes->get('class') }}" 
  href="{{ $attributes->get('href') ?? '#' }}"
  {{ $attributes }}
>
  {{ $slot }}
</a>