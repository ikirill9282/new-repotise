@if(auth()->check())
  <a 
    href="#" 
    class="span_buy favorite-button {{ auth()->user()->hasFavorite($item_id, $type) ? 'favorite-active' : '' }}" 
    data-item="{{ \App\Helpers\CustomEncrypt::encrypt(['type' => $type, 'item_id' => $item_id]) }}"
  >
    @include('icons.favorite', ['stroke' => isset($stroke) ? $stroke : '#000'])
  </a>
@endif