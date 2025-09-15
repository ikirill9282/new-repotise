@props([
  'name' => uniqid(),
  'title' => null,
  'label' => null,
  'value' => null,
  'options' => [
    10 => '10 Days',
    20 => '20 Days',
    30 => '30 Days',
  ],
])

<div 
  x-data="{
    value: null,
    label: {{ $label ? "'$label'" : 'null' }},
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
    setVal(val, label = null) {
      this.value = val;
      this.label = label ? label : '';
      this.$refs.placeholder.classList.add('!text-black')
    }
  }"
  x-init="() => {
    if (value === null && label === null) {
      setVal('{{ array_key_first($options) }}', '{{ $options[array_key_first($options)] }}');
    }
  }"
  class="w-full group text-sm sm:text-base"
>
  <input x-bind:value="value" type="hidden" name="{{ $name }}" {{ $attributes }}>

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
        @foreach($options as $val => $label)
          <div 
            x-on:click="() => {
              setVal('{{$val}}', '{{ $label }}');
              toggle();
            }" 
            class="px-4 py-2 hover:cursor-pointer hover:text-black"
          >
            {{ $label }}
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>