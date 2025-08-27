@props([
  'type' => 'text',
  'name' => uniqid(),
  'label' => null,
])

<div class="relative w-full {{ $attributes->get('class') }}">
  <div class="relative flex justify-between items-center bg-light !p-4 !pr-8">
    <div class="">{{ $label }}</div>
    <label for="{{ $name }}" class="leading-0 hover:cursor-pointer">
        <input type="checkbox" id="{{ $name }}" class="creatorPage__aside-connectSocials-item-checkbox">
        <span class="toggle-switch"></span>
    </label>
    <x-tooltip class="!right-2" message="tooltip"></x-tooltip>
  </div>
</div>