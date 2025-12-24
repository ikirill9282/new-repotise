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
  'value' => null,
  // When true, the control shrinks to content (useful for "Sort by" UI),
  // and the arrow stays next to the text instead of being pushed to the far right.
  'compact' => false,
])
<div 
  x-data="{
    value: null,
    label: {{ $label ? "'$label'" : 'null' }},
    placeholder: {{ $label ? "'$label'" : 'null' }},
    close() {
      const dropdown = this.$refs.dropdown;
      dropdown.classList.remove('opened');
      dropdown.removeAttribute('style');
    },
    closeAllOtherSelects() {
      // Закрываем все другие открытые селекты
      document.querySelectorAll('.select-dropdown.opened').forEach(el => {
        if (el !== this.$refs.dropdown) {
          el.classList.remove('opened');
          el.removeAttribute('style');
        }
      });
    },
    toggle() {
      const dropdown = this.$refs.dropdown;
      const height = this.$refs.dropdownContent?.offsetHeight ?? 0;
      
      if (dropdown.classList.contains('opened')) {
        this.close();
      } else {
        // Закрываем все другие селекты перед открытием этого
        this.closeAllOtherSelects();
        dropdown.classList.add('opened');
        dropdown.style.height = `${height}px`;
      }
    },
    setVal(val, label) {
      this.value = val;
      this.label = label;
      if (this.$refs.placeholder && this.$refs.placeholder.classList) {
        this.$refs.placeholder.classList.add('!text-black');
      }
      
      const event = new Event('input', {
        bubbles: true,
        cancellable: true,
      });

      if (this.$refs.input) {
        this.$refs.input.value = this.value;
        this.$refs.input.dispatchEvent(event);
      }
    }
  }"
  x-init="() => {
    const initValue = () => {
      if (!$refs.input) return;
      
      const val = $refs.input.value ?? '';
      if (val) {
        const lab = $refs.dropdownContent?.querySelector(`div[data-key='${val}']`)?.innerHTML.trim();
        if (lab) {
          setVal(val, lab);
        }
      } else if (label) {
        // Если есть начальный label, но нет значения, устанавливаем label
        if ($refs.placeholder && $refs.placeholder.classList) {
          $refs.placeholder.classList.add('!text-black');
        }
      }
    };
    
    // Инициализируем значение после того, как Alpine.js полностью загрузится
    $nextTick(() => {
      initValue();
    });

    Livewire.on('resetForm', () => {
      value = null;
      label = placeholder;
    });

    // Глобальный обработчик для закрытия при клике вне компонента
    const handleClickOutside = (event) => {
      // Проверяем, что элемент все еще существует
      if (!this.$el || !this.$refs || !this.$refs.dropdown) {
        return;
      }
      
      const dropdown = this.$refs.dropdown;
      const isOpen = dropdown && dropdown.classList.contains('opened');
      
      if (isOpen && this.$el && !this.$el.contains(event.target)) {
        this.close();
      }
    };
    
    // Сохраняем ссылку на обработчик для последующего удаления
    const clickHandler = handleClickOutside.bind(this);
    
    // Используем capture фазу для более надежного определения клика вне
    document.addEventListener('click', clickHandler, true);
    
    // Очистка при удалении компонента
    if (this.$el) {
      this.$el.addEventListener('alpine:destroyed', () => {
        document.removeEventListener('click', clickHandler, true);
      });
    }
  }"
  @click.outside="close()"
  class="{{ $compact ? 'inline-block' : 'w-full' }} group text-sm sm:text-base"
>
  <input x-ref="input" type="hidden" name="{{ $name }}" value="{{ isset($value) ? $value : '' }}" {{ $attributes }}>

  @if($title)
    <label class="text-sm sm:text-base text-gray mb-1.5" for="{{ $name }}">{{ $title }}</label>
  @endif

  <div 
    class="{{ $compact ? 'w-fit px-3 py-0' : 'w-full !p-3' }} rounded bg-light @error($name) border !border-red-500 @enderror"
    x-data="{compact: {{ $compact ? 'true' : 'false' }}}"
  >
    <div 
      x-ref="placeholder"
      x-on:click="toggle()" 
      class="transition flex {{ $compact ? 'justify-start items-baseline gap-2' : 'justify-between items-center' }} relative {{ $compact ? '' : 'pr-6' }}
            hover:cursor-pointer hover:text-black
            {{ $attributes->get('labelClass') }}
            "
      >
      <span 
        x-html="label" 
        x-bind:class="value === null ? '!text-gray' : ''"
        class="{{ $compact ? 'leading-tight' : '' }}"
        x-ref="textSpan"
      >{{ $label }}</span>
      <span 
        class="transition group-has-[.opened]:rotate-180 flex-shrink-0 {{ $compact ? 'self-baseline' : '' }}"
        x-ref="arrowSpan"
      >
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
      class="select-dropdown absolute w-full h-0 bottom-0 left-0 translate-y-full overflow-hidden
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