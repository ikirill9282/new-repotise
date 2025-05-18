@if(auth()->check())
  <a 
    href="#" 
    class="span_buy favorite-button {{ auth()->user()->hasFavorite($item_id, $type) ? 'favorite-active' : '' }} {{ isset($class) ? $class : '' }}" 
    data-item="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['type' => $type, 'item_id' => $item_id]) }}"
    data-key="{{ \App\Helpers\CustomEncrypt::generateStaticUrlHas(['type' => $type, 'item_id' => $item_id]) }}"
  >
    @include('icons.favorite', ['stroke' => isset($stroke) ? $stroke : '#000'])
  </a>
@endif