@props([
  'link' => '#',
  'class' => '',
])

<a 
  href="{{ $link }}" 
  class="w-full px-10 py-1.5 rounded transition 
        text-black !bg-light hover:!text-white hover:!bg-secondary
        {{ $class }}
        "
  {{ $attributes }}
>
  {{ $slot ?? '' }}
</a>