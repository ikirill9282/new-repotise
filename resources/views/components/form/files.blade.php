@props([
  'count' => 1,
])

<div class="">
  @for($i = 0; $i < $count; $i++)
    <div class="relative">
      <input type="file" id="file-input-{{ $i }}" class="w-0 h-0 opacity-0 absolute">
      <label 
        for="file-input-{{ $i }}"
        class="p-3 rounded-lg bg-light hover:cursor-pointer text-gray transition hover:text-active"
        >
          @include('icons.download')
        </label>
    </div>
  @endfor
</div>