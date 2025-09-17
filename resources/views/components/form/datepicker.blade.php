@props([
  'id' => 'id'.uniqid(),
  'name' => uniqid(),
  'label' => null,
])

<div class="relative"
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
  <div x-data="" class="bg-light !p-3 !pr-9 rounded-lg relative flex justify-between items-center">
    <div class="grow">
      <input x-ref="input" name="{{ $name }}" class="w-full !outline-0" type="text" id="{{ $id }}" {{ $attributes }}>
    </div>
    <div x-on:click.prevent="() => $refs.input.focus()" class="!text-gray hover:cursor-pointer !px-1">
      @include('icons.calendar')
    </div>
    <x-tooltip class="!right-3" message="tooltip"></x-tooltip>
  </div>
</div>