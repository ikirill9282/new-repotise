@props([
  'tag' => 'div',
])

<{{ $tag }} class="!text-2xl !font-bold {{ $attributes->get('class') }}" {{ $attributes }} >{{ $slot }}</{{ $tag }}>
