@props([
  'label' => null,
  'placeholder' => null,
  'tooltip' => true,
  'max' => 500,
  'name' => null,
])
<div  class="" 
    x-ref="base"
    x-data="{
      len: 0,
      max: {{ $max }},
      setLen() {
        const len = this.getLen();
        if (this.len <= this.max) {
          this.len = len;
        }
      },
      getLen() {
        return $refs.base.querySelector('.textarea-counter').value.length;
      }
    }"
    x-init="() => {
      setTimeout(() => setLen(), 300);
      window.addEventListener('DOMContentLoaded', () => {
        setLen();
        Livewire.hook('morphed', () => {
          setLen();
        });
      });
    }"
  >
  @if($attributes->get('wire:model'))
    @php
      $var = $attributes->get('wire:model');
      $val = data_get($this, $var);
      if (!empty($val)) $placeholder = null;
    @endphp
  @endif
  <x-form.textarea 
    :placeholder="$placeholder"
    :label="$label"
    :tooltip="$tooltip"
    class="textarea-counter min-h-24"
    x-on:input="setLen"
    {{ $attributes }}
  ></x-form.textarea>

  <div class="text-sm !text-gray text-right mt-2">
    <span x-html="len"></span>
    <span>/</span>
    <span x-html="max"></span>
  </div>

  @error($name)
    <div class="text-red-500">{{ $message }}</div>
  @enderror
</div>