@props([
  'type' => 'text',
  'name' => uniqid(),
  'label' => null,
  'value' => null,
  'tooltip' => true,
  'tooltipModal' => false,
  'tooltipText' => 'tooltip',
  'inputWrapClass' => '',
])

<div class="relative w-full group text-sm sm:text-base {{ $attributes->get('class') }}">
  @if($label) 
    <label for="{{ $name }}" class="!mb-2 text-gray hover:cursor-pointer">{{ $label }}</label>
  @endif
  <div class="relative bg-light rounded !p-3 !pr-9 transition 
              {{ $inputWrapClass }}
            ">
    <input 
      type="{{ $type }}" 
      name="{{ $name }}"
      id="{{ $name }}"
      class="w-full outline-0 
            {{ $attributes->get('inputClass') }}"
      value="{{ $value }}"
      {{ $attributes }}
    >
    @if($tooltip)
      <x-tooltip 
        class="!right-3" 
        :message="$tooltipText" 
        :tooltipClass="$tooltipModal ? 'sm:!max-w-sm !transform-none !translate-x-[-100%] after:!hidden' : ''" 
        ></x-tooltip>
    @endif
  </div>
</div>