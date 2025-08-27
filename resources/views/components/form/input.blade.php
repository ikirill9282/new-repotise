@props([
  'type' => 'text',
  'name' => uniqid(),
  'label' => null,
  'value' => null,
])

<div class="relative w-full {{ $attributes->get('class') }}">
  <label for="{{ $name }}" class="!mb-2 text-gray hover:cursor-pointer">{{ $label }}</label>
  <div class="relative bg-light rounded !p-4 !pr-8 ">
    <input 
      type="{{ $type }}" 
      name="{{ $name }}"
      id="{{ $name }}"
      class="w-full outline-0 {{ $attributes->get('inputClass') }}"
      value="{{ $value }}"
      {{ $attributes }}
    >
    <x-tooltip class="!right-2" message="tooltip"></x-tooltip>
  </div>
</div>