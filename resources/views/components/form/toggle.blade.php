@props([
  'type' => 'text',
  'name' => uniqid(),
  'label' => null,
  'tooltip' => true,
  'tooltipText' => null,
])

@php
  $inputAttributes = $attributes->except(['class', 'wrapClass', 'labelClass', 'tooltip', 'inputClass']);
  $inputClass = trim('creatorPage__aside-connectSocials-item-checkbox ' . ($attributes->get('inputClass') ?? ''));
@endphp

<div class="relative w-full text-sm sm:text-base group {{ $attributes->get('class') }}">
  <div class="relative flex justify-between items-center bg-light !p-4 !pr-8 rounded {{ $attributes->get('wrapClass') }}">
    <label 
      for="{{ $name }}" 
      class="hover:cursor-pointer w-full !flex justify-between items-center mr-4
            text-gray transition group-has-checked:text-black
            {{ $attributes->get('labelClass') }}
            "
    >
        <div class="px-1">{{ $label }}</div>
        <input 
          type="checkbox" 
          id="{{ $name }}" 
          class="{{ $inputClass }}"
          {{ $inputAttributes }}
        >
        <span class="toggle-switch shrink-0"></span>
    </label>
    @if($tooltip && filled($tooltipText))
      <x-tooltip class="!right-4" :message="$tooltipText"></x-tooltip>
    @endif
  </div>
</div>
