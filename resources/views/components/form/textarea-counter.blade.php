@props([
  'label' => null,
  'placeholder' => null,
  'tooltip' => true,
])
<div  class="" 
    x-ref="base"
    x-data="{
      len: 0,
      max: 500,
      setLen(val) {
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
      window.addEventListener('DOMContentLoaded', () => {
        setLen();
        Livewire.hook('morphed', () => {
          setLen();
        });
      });
    }"
  >
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
</div>