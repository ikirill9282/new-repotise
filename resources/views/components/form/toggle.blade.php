@props([
  'type' => 'text',
  'name' => uniqid(),
  'label' => null,
])

<div class="relative w-full group {{ $attributes->get('class') }}">
  <div class="relative flex justify-between items-center bg-light !p-4 !pr-8 rounded {{ $attributes->get('wrapClass') }}">
    <label 
      for="{{ $name }}" 
      class="hover:cursor-pointer w-full !flex justify-between items-center mr-4
            text-gray transition group-has-checked:text-black
            {{ $attributes->get('labelClass') }}
            "
    >
        <div class="">{{ $label }}</div>
        <input type="checkbox" id="{{ $name }}" class="creatorPage__aside-connectSocials-item-checkbox">
        <span class="toggle-switch shrink-0"></span>
    </label>
    <x-tooltip class="!right-4" message="tooltip"></x-tooltip>
  </div>
</div>