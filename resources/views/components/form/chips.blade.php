@props([
  'label' => null,
  'id' => uniqid(),
  'placeholder' => '',
  'source' => null,
  'max' => 5,
])

<div class="relative dropdown text-sm sm:text-base"
  x-data="{
    selected: [],
    options: [],
    error: null,
    load(query = '') {
      axios.get('/api/data/{{ $source }}', { params: { q: query } })
        .then(response => {
          this.options = response.data.length 
            ? response.data
            : this.getEmptyOptions();
        })
        .catch(error => console.log(error));
    },
    pushVal(val) {
      if (this.selected.length >= {{ $max }}) {
        this.error = 'Maximum tags';
        return ;
      }

      const formatted = {
        key: val,
        label: val,
      };

      if (!this.hasVal(formatted)) {
        this.selected.push(formatted);
        this.error = null;
      }
    },
    addVal(val) {
      if (val.key !== 'empty' && !this.hasVal(val)) {
        if (this.selected.length >= {{ $max }}) {
          this.error = 'Maximum tags';
          return ;
        }
        this.selected.push(val);
        this.error = null;
      } else {
        this.dropVal(val); 
      }
    },
    dropVal(val) {
      this.selected = this.selected.filter(elem => elem !== val);
      this.error = null;
    },
    hasVal(val) {
      return this.selected.find(item => item.key == val.key) !== undefined;
    },
    showList() {
      this.$refs.dropdown.classList.remove('!h-0');
      this.$refs.close.classList.remove('hidden');
      setTimeout(() => this.$refs.dropdown.classList.remove('opacity-0'), 100);

      const checkHide = (evt) => {
        if (!evt.target.classList.contains('dropdown') && !evt.target.closest('.dropdown')) {
          this.hideList();
          window.removeEventListener('click', checkHide);
        }
      }

      window.addEventListener('click', checkHide);
    },
    hideList() {
      this.$refs.dropdown.classList.add('opacity-0');
      this.$refs.close.classList.add('hidden');
      setTimeout(() => this.$refs.dropdown.classList.add('!h-0'), 300);
      this.error = null;
    },
    getEmptyOptions() {
      return [
        { key: 'empty', label: 'No Searched Results...' }
      ];
    },
    isEmptyOptions() {
      return !this.options.filter(elem => elem.key !== 'empty').lenght;
    },
    setVal(val) {
      this.$refs.value.value = val;
    },
  }"

  x-init="() => {
    load();
    $watch('selected', value => {
      const val = value.map(elem => elem.key).join(',');
      setVal(val);
    })
  }"
  >

  <input x-ref="value" type="hidden" {{ $attributes }} >

  @if($label)
    <label class="text-gray !mb-2" for="{{ $id }}">{{ $label }}</label>
  @endif
  
  {{-- INPUT --}}
  <div class="bg-light relative rounded-lg flex justify-start items-center">
    <div class="!pl-4">
      @include('icons.search')
    </div>
    <div class="w-full !px-3 !py-4 !pr-9 flex items-cener justify-between">
      <input 
        x-ref="input"
        x-on:focus="showList()"
        x-on:input="(evt) => {
          evt.target.value = evt.target.value.replace(',', '');
          load(evt.target.value);
        }"
        x-on:keydown.enter.prevent="(evt) => {
          console.log('ok');
          pushVal(evt.target.value);
          $refs.input.value = '';
          if (isEmptyOptions()) load();
        }"
        type="text" 
        name="{{ $id }}" 
        id="{{ $id }}" 
        class="outline-0 w-full inline-block"
        placeholder="{{ $placeholder }}"
      >

      <div 
        x-ref="close" 
        x-on:click="() => {
          $refs.input.value = '';
          hideList();
          load();
        }" 
        class="hidden text-gray !mx-1 !mt-0.5 transition hover:cursor-pointer hover:text-black"
      >
        @include('icons.close', ['width' => 14, 'height' => 14])
      </div>
    </div>

    <x-tooltip class="!right-3" message="tooltip"></x-tooltip>
  </div>

  <template x-if="error && error.length">
    <div x-text="error" class="text-red-500 !mb-2"></div>
  </template>

  @if($attributes->get('name'))
    @error($attributes->get('name'))
      <div class="text-red-500">{{ $message }}</div>
    @enderror
  @endif

  {{-- OPTIONS --}}
  <div x-ref="dropdown" class="absolute w-full bottom-0 left-0 translate-y-full bg-light z-120
      rounded-lg overflow-hidden !h-0 opacity-0 transition max-h-48 overflow-x-hidden overflow-y-auto
      scrollbar-custom"
    >
    <template x-for="option in options">
      <div 
        x-bind:data-value="option.value"
        x-on:click="addVal(option)"
        x-bind:class="[
          'group !p-3 transition hover:ring-2 ring-active hover:!text-active hover:bg-active/10 cursor-pointer',
          (hasVal(option)) ? '!bg-active/10 !text-active' : '!text-gray',
        ]"
      >
        <span x-text="option.label" class="!block w-full"></span>
      </div>
    </template>
  </div>

  {{-- SELECTED --}}
  <div class="flex justify-start items-stretch flex-wrap !gap-2 !pb-2 !mt-2">
    <template x-for="item in selected">
      <div class="!px-2.5 !py-[3px] rounded-full text-sm text-light bg-gray flex justify-start items-center !gap-2">
        <span x-text="item.label"></span>
        <span x-on:click="dropVal(item)" class="hover:cursor-pointer">
          @include('icons.close', ['width' => 8, 'height' => 8])
        </span>
      </div>
    </template>
  </div>

</div>