@props(['href' => '#'])

<a href="{{ $href }}" class="!text-gray hover:!text-active pb-0.5 border-b-1 border-dashed">{{ $slot }}</a>