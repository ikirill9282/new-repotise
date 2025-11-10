@props([
  'outlined' => false,
  'second' => false,
  'gray' => false,
  'disabled' => false,
])

@php
    $baseClasses = 'main-btn !p-2.5 w-full !rounded !text-sm sm:!text-base';
    $classes = trim(collect([
        $baseClasses,
        $attributes->get('class'),
        $outlined ? 'main-btn-outlined' : '',
        $second ? '!bg-second !border-second hover:!bg-active hover:!border-active' : '',
        $gray ? '!bg-light !text-gray !border-light hover:!border-gray hover:!text-second hover:!shadow-none ' : '',
        $disabled ? ' pointer-events-none' : '',
    ])->filter()->implode(' '));

    $isButtonElement = $attributes->has('type');
@endphp

@if($isButtonElement)
  <button
    {{ $attributes->except('href')->merge(['class' => $classes]) }}
    @disabled($disabled)
  >
    {{ $slot }}
  </button>
@else
  <a 
    {{ $attributes->merge([
      'class' => $classes,
      'href' => $attributes->get('href') ?? '#',
    ]) }}
  >
    {{ $slot }}
  </a>
@endif
