@props([
  'id' => "checkbox-" . uniqid(),
  'label' => '',
  'tooltip' => false,
])

<div class="group relative text-sm sm:text-base">
  <input 
    type="checkbox" 
    id="{{ $id }}"
    class="w-0 h-0 opacity-0 absolute"
    {{ $attributes }}
  >
  <label 
    for="{{ $id }}"
    class="text-gray transition hover:cursor-pointer hover:text-active pl-6 select-none
      before:content-[''] before:absolute before:w-4 before:h-4 before:top-[50%] before:translate-y-[-50%] before:transition
      before:left-0 before:border before:rounded before:border-gray hover:before:border-active
      group-has-checked:before:bg-active group-has-checked:before:border-active
      {{ $attributes->get('class') }}
    "
    >
      <div class="absolute left-0 p-[2px] top-[50%] translate-y-[-50%] text-white !opacity-0 group-has-checked:!opacity-100">
        @include('icons.check')
      </div>
      {{ $label }}
  </label>

  @if($tooltip)
    <x-tooltip message="tooltip" class="right-4" />
  @endif
</div>