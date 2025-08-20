@props([
  'active' => 0
])
<div class="flex justify-start items-center gap-1">
  @for($i = 1; $i <= 10; $i++)
   <div class="w-2.5 h-2.5 rounded-full @if($i <= $active) bg-active @else bg-light  @endif"></div>
  @endfor
</div>