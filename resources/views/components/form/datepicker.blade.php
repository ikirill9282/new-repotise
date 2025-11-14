@props([
  'id' => 'id'.uniqid(),
  'name' => uniqid(),
  'label' => null,
  'tooltip' => false,
  'tooltipText' => null,
])

<div class="relative datepicker"
  x-data="{
    picker: null,
  }"
  x-init="() => {
    picker = createDatePicker('#{{ $id }}');
  }"
>
  @if($label)
    <label class="!text-gray !mb-2" for="{{ $id }}">{{ $label }}</label>
  @endif
  <div class="bg-light !p-3 !pr-9 rounded-lg relative flex justify-between items-center datepicker-input">
    <div class="grow">
      <input x-ref="input" name="{{ $name }}" class="w-full !outline-0 datepicker-input" type="text" id="{{ $id }}" {{ $attributes }}>
    </div>
    <div x-on:click.prevent="() => $refs.input.focus()" class="!text-gray hover:cursor-pointer !px-1">
      @include('icons.calendar')
    </div>
    @if($tooltip && filled($tooltipText))
      <x-tooltip class="!right-3" :message="$tooltipText"></x-tooltip>
    @endif
  </div>
</div>