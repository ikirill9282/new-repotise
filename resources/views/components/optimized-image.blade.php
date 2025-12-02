@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'lazy' => true,
    'fetchpriority' => null, // 'high' for LCP images, 'low' for below-fold
])

@php
    $src = $src ?: '/storage/images/default_product.png';
@endphp

<img 
    src="{{ url($src) }}" 
    alt="{{ $alt }}"
    class="{{ $class }}"
    @if($lazy) loading="lazy" @endif
    @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    decoding="async"
>

