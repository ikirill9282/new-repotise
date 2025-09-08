@props(['href' => '#', 'border' => true])

<a href="{{ $href }}" class="!text-gray hover:!text-active hover:!border-active pb-0.5 @if($border) border-b-1 border-dashed @endif {{ $attributes->get('class') }}" {{ $attributes }}>{{ $slot }}</a>