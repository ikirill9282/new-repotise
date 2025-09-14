@props([
  'tag' => 'div',
])

<{{ $tag }} class="!text-2xl md:!text-3xl !font-bold {{ $attributes->get('class') }}" {{ $attributes }} >{{ $slot }}</{{ $tag }}>
