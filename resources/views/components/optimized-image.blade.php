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
    $lazyAttr = $lazy ? 'loading="lazy"' : '';
    $fetchpriorityAttr = $fetchpriority ? "fetchpriority=\"{$fetchpriority}\"" : '';
    $widthAttr = $width ? "width=\"{$width}\"" : '';
    $heightAttr = $height ? "height=\"{$height}\"" : '';
@endphp

<img 
    src="{{ url($src) }}" 
    alt="{{ $alt }}"
    class="{{ $class }}"
    {{ $lazyAttr }}
    {{ $fetchpriorityAttr }}
    {{ $widthAttr }}
    {{ $heightAttr }}
    decoding="async"
>

