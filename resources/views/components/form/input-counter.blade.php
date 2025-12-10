@props([
  'label' => null,
  'placeholder' => null,
  'tooltip' => false,
  'max' => 70,
  'name' => null,
])
@php
  $inputName = $name ?? uniqid('input-counter-');
  $wireModel = $attributes->get('wire:model');
  $initialValue = '';
  if ($wireModel) {
    $var = $wireModel;
    $val = data_get($this, $var);
    $initialValue = $val ?? '';
    if (!empty($val)) $placeholder = null;
  }
@endphp

<div  class="" 
    x-ref="base"
    x-data="{
      len: {{ strlen($initialValue) }},
      max: {{ $max }},
      getInput() {
        if (!this.$refs.base) return null;
        return this.$refs.base.querySelector('input[name={{ $inputName }}]');
      },
      updateLen() {
        const input = this.getInput();
        if (input) {
          this.len = input.value.length;
        }
      },
      initCounter() {
        const input = this.getInput();
        if (input) {
          this.len = input.value.length;
          input.addEventListener('input', () => {
            this.len = input.value.length;
          });
        }
      }
    }"
    x-init="() => {
      setTimeout(() => {
        if ($refs.base) {
          initCounter();
        }
      }, 100);
      Livewire.hook('morphed', () => {
        setTimeout(() => {
          if ($refs.base) {
            updateLen();
          }
        }, 100);
      });
      Livewire.hook('message.processed', () => {
        setTimeout(() => {
          if ($refs.base) {
            updateLen();
          }
        }, 100);
      });
    }"
  >
  <x-form.input 
    :placeholder="$placeholder"
    :label="$label"
    :tooltip="$tooltip"
    :name="$inputName"
    inputClass="input-counter"
    maxlength="{{ $max }}"
    {{ $attributes }}
  ></x-form.input>

  <div class="text-sm !text-gray text-right mt-2">
    <span x-html="len"></span>
    <span>/</span>
    <span x-html="max"></span>
  </div>
</div>
