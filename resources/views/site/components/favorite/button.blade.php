@props([
  'width' => 26,
  'height' => 26,
  'stroke' => '#000',
])
@if(auth()->check())
  <a 
    href="#" 
    class="span_buy favorite-button !text-transparent hover:!text-red {{ auth()->user()->hasFavorite($item_id, $type) ? 'favorite-active' : '' }} {{ isset($class) ? $class : '' }}" 
    data-item="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['type' => $type, 'item_id' => $item_id]) }}"
    data-key="{{ \App\Helpers\CustomEncrypt::generateStaticUrlHas(['type' => $type, 'item_id' => $item_id]) }}"
  >
    @include('icons.favorite', [
      'stroke' => $stroke,
      'width' => $width,
      'height' => $height,
    ])
  </a>
@endif