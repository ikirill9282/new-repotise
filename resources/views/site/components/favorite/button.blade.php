@if(auth()->check())
  <a 
    href="#" 
    class="span_buy favorite-button {{ auth()->user()->hasFavorite($item_id, $type) ? 'favorite-active' : '' }} {{ isset($class) ? $class : '' }}" 
    data-item="{{ \App\Helpers\CustomEncrypt::encrypt(['type' => $type, 'item_id' => $item_id]) }}"
    data-key="{{ $type }}-{{ $item_id }}"
  >
    @include('icons.favorite', ['stroke' => isset($stroke) ? $stroke : '#000'])
  </a>
@endif