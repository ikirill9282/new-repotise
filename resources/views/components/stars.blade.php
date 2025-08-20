@props([
  'active' => 0,
])

<div class="flex">
  @for($i = 1; $i <= 5; $i++)
    <span class="@if($i <= $active) text-yellow @else text-transparent @endif">
      @include('icons.star', ['width' => 15, 'height' => 15])
    </span>
  @endfor
</div>