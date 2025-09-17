@props([
  'id' => 'id'.uniqid(),
  'placeholder' => '',
  'label' => null,
  'type' => 'media'
])

<div x-data="" class="relative group">
  <input x-ref="file" type="file" id="{{ $id }}" class="w-0 h-0 opacity-0 absolute" {{ $attributes }}>
  @if($label)
    <div x-on:click.prevent="() => $refs.file.click()" class="!text-gray !mb-2 hover:cursor-pointer">{{ $label }}</div>
  @endif
  <label 
    for="{{ $id }}"
    class="p-3 rounded-lg bg-light group-hover:cursor-pointer text-gray transition group-hover:text-active"
    >
      <div class="flex justify-start items-center !gap-2">
        <div class="">
          @if($type == 'file')
            @include('icons.document')
          @else
            @include('icons.download')
          @endif
        </div>
        @if($placeholder)
          <div class="">{{ $placeholder }}</div>
        @endif
      </div>
    </label>
</div>