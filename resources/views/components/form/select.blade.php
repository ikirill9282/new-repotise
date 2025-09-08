@props([
  'name' => null,
  'label' => null,
  'options' => [],
])

<div 
  x-data="{
    value: null,
    label: '{{ $label }}',
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
    setVal(val) {
      this.value = val;
      this.label = `${val} days`;
      this.toggle();
      this.$refs.placeholder.classList.add('!text-black')
    }
  }"
  class="w-full group"
>
  <input x-bind:value="value" type="hidden" name="{{ $name }}" {{ $attributes }}>
  <div class="w-full !p-4 rounded bg-light">
    <div 
      x-ref="placeholder"
      x-on:click="toggle()" 
      class="transition flex justify-between items-center relative !pr-8 
            hover:cursor-pointer hover:text-black
            {{ $attributes->get('labelClass') }}
            "
      >
      <span x-html="label">{{ $label }}</span>
      <span class="transition group-has-[.opened]:rotate-180">@include('icons.arrow_down', ['width' => 18, 'height' => 18])</span>
      <x-tooltip message="tooltip" class=""></x-tooltip>
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
        <div x-on:click="setVal(10)" class="px-4 py-2 hover:cursor-pointer hover:text-black">10 days</div>
        <div x-on:click="setVal(20)" class="px-4 py-2 hover:cursor-pointer hover:text-black">30 days</div>
        <div x-on:click="setVal(30)" class="px-4 py-2 hover:cursor-pointer hover:text-black">90 days</div>
      </div>
    </div>
  </div>
</div>