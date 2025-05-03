<div 
  class="parallax {{ isset($class) ? $class : '' }}"
  @if (isset($attributes) && is_array($attributes) && !empty($attributes))
    @foreach($attributes as $key => $value)
      {{ $key }}="{{ $value }}"
    @endforeach
  @endif
  ></div>