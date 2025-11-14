@props([
  'name' => uniqid(),
  'title' => null,
  'label' => null,
  'options' => [
    10 => '10 Days',
    20 => '20 Days',
    30 => '30 Days',
  ],
  'name' => null,
	'tooltip' => true,
  'tooltipModal' => false,
  'tooltipText' => null,
])
<div 
  x-data="{
    value: null,
    label: {{ $label ? "'$label'" : 'null' }},
    placeholder: {{ $label ? "'$label'" : 'null' }},
    toggle() {
      const dropdown = this.$refs.dropdown;
      const height = this.$refs.dropdownContent?.offsetHeight ?? 0;
      
      if (dropdown.classList.contains('opened')) {
        dropdown.classList.remove('opened');
        dropdown.removeAttribute('style');
      } else {
        dropdown.classList.add('opened');
        dropdown.style.height = `${height}px`;
      }
    },
    setVal(val, label) {
      this.value = val;
      this.label = label;
      this.$refs.placeholder.classList.add('!text-black');
      
      const event = new Event('input', {
        bubbles: true,
        cancellable: true,
      });

      this.$refs.input.value = this.value;
      this.$refs.input.dispatchEvent(event);
    }
  }"
  x-init="() => {
    window.addEventListener('DOMContentLoaded', () => {
      const val = $refs.input.value ?? '';
      const lab = $refs.dropdownContent.querySelector(`div[data-key='${val}']`)?.innerHTML.trim();
      if (lab) {
        setVal(val, lab);
      }

      Livewire.on('resetForm', () => {
        value = null;
        label = placeholder;
      });
    });
  }"
  class="w-full group text-sm sm:text-base"
>
  <input x-ref="input" type="hidden" name="{{ $name }}" {{ $attributes }}>

  @if($title)
    <label class="text-sm sm:text-base text-gray mb-1.5" for="{{ $name }}">{{ $title }}</label>
  @endif

  <div class="w-full !p-3 rounded bg-light">
    <div 
      x-ref="placeholder"
      x-on:click="toggle()" 
      class="transition flex justify-between items-center relative !pr-6 
            hover:cursor-pointer hover:text-black
            {{ $attributes->get('labelClass') }}
            "
      >
      <span x-html="label" x-bind:class="value === null ? '!text-gray' : ''">{{ $label }}</span>
      <span class="transition group-has-[.opened]:rotate-180">
        @include('icons.arrow_down', ['width' => 18, 'height' => 18])
      </span>
			@if($tooltip && filled($tooltipText))
				<x-tooltip 
					class="!right-3" 
					:message="$tooltipText" 
					:tooltipClass="$tooltipModal ? 'sm:!max-w-sm !transform-none !translate-x-[-100%] after:!hidden' : ''" 
					></x-tooltip>
			@endif
    </div>
  </div>
  <div class="w-full relative">
    <div 
      x-ref="dropdown" 
      class="absolute w-full h-0 bottom-0 left-0 translate-y-full overflow-hidden
          bg-light rounded-bl rounded-br z-120 shadow
          "
        >
      <div x-ref="dropdownContent" class="flex flex-col text-gray">
        @foreach($options as $val => $label)
          <div 
            x-on:click="() => {
              setVal('{{$val}}', '{{ $label }}');
              toggle();
            }"
            data-key="{{ $val }}"
            class="px-4 py-2 hover:cursor-pointer hover:text-black"
          >
            {{ $label }}
          </div>
        @endforeach
      </div>
    </div>
  </div>

  @error($name)
    <div class="!mt-2 text-red-500">{{ $message }}</div>
  @enderror
</div>