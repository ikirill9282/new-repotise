@props([
  'label' => null,
  'placeholder' => '',
  'name' => 'textarea',
  'id' => null,
  'tooltip' => false,
])

<div 
  x-data="{
    hide() {
      this.$refs.placeholder.classList.add('opacity-0');
      this.$refs.placeholder.classList.remove('opacity-100');
    },
    show() {
      if (!this.$refs.textarea.value.length) {
        this.$refs.placeholder.classList.add('opacity-100');
        this.$refs.placeholder.classList.remove('opacity-0'); 
      }
    },
  }"
  x-init="() => {
    window.addEventListener('DOMContentLoaded', () => {
      if ($refs.textarea.value.length) {
        hide();
      }
      Livewire.hook('morphed', () => {
        if ($refs.textarea.value.length) {
          hide();
        }
      });
    });
  }"
  class=""
>
  @if($label)
    <label class="text-gray text-sm sm:text-base mb-1" for="{{ $id }}">{{ $label }}</label>
  @endif
  <div class="relative bg-light rounded-lg !p-3 flex items-start justify-between @if($tooltip) !gap-3 @endif ">
    <div x-ref="placeholder" class="absolute top-3 left-3 max-w-5/6 !text-gray transition">
      {!! $placeholder !!}
    </div>
    {{-- @dump($attributes) --}}
    <textarea
      x-ref="textarea"
      x-on:change="() => console.log('test')"
      x-on:focus="hide()"
      x-on:focusout="show()"
      class="w-full relative z-10 bg-transparent outline-0 {{ $attributes->get('class') }}"
      name="{{ $name }}"
      id="{{ $id }}"
      {{ $attributes }}
    >{{ $slot }}</textarea>
    
    @if($tooltip)
      <div class="relative !w-4 !h-4">
        <x-tooltip message="tooltip"></x-tooltip>
      </div>
    @endif
  </div>
</div>