@props([
  'item_id',
  'width' => 26,
  'height' => 26,
  'stroke' => '#000',
  'class' => '',
  'is_following' => false,
])

@if(auth()->check())
  @php
    $resource = \Illuminate\Support\Facades\Crypt::encrypt($item_id);
    $group = \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $item_id]);
  @endphp
  <a 
    href="#"
    class="span_buy favorite-button follow-btn {{ $is_following ? 'favorite-active' : '' }} {{ $class }}"
    data-resource="{{ $resource }}"
    data-group="{{ $group }}"
    data-mode="icon"
    data-follow="1"
    aria-pressed="{{ $is_following ? 'true' : 'false' }}"
  >
    @include('icons.favorite', [
      'stroke' => $stroke,
      'width' => $width,
      'height' => $height,
    ])
  </a>
@endif

