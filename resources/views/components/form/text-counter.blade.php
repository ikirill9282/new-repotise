@props([
  'default' => '',
  'max' => 100,
  'placeholder' => '',
  'label' => null,
  'id' => uniqid(),
  'emoji' => false,
  'author' => null,
  'button' => true,
  'name' => 'text',
])

<div class="w-full flex flex-col justify-start items-stretch gap-1 group">
    @if($label)
      <label class="text-sm sm:text-base text-gray" for="{{ $id }}">{{ $label }}</label>
    @endif
    <div 
        x-data="{
          value: '{{ addslashes($default) }}',
          maxLength: {{ $max }},
          resize() {
            if (this.$refs.ta) {
              this.$refs.ta.style.height = 'auto';
              this.$refs.ta.style.height = this.$refs.ta.scrollHeight + 'px';
            }
          }
        }"
        x-init="
          resize();
          Livewire.hook('morphed', () => {
            setTimeout(() => resize(), 0);
          });
          Livewire.hook('processed', () => {
            setTimeout(() => resize(), 0);
          });
        "
        class="relative w-full flex items-end md:items-start justify-start grow
              flex-col-reverse md:flex-row gap-2 sm:gap-3
              md:bg-light md:rounded-lg md:p-3
              "
    >
        @if($author)
          <div class="hidden md:block">{{ $author }}</div>
        @endif

        <div class="bg-light w-full !grow rounded-lg !p-3 md:!p-0 !leading-0">
          <textarea 
              name="{{ $name }}" 
              rows="1"
              x-ref="ta" 
              x-model="value" 
              x-on:input="resize()"
              id="{{ $id }}" 
              :maxlength="maxLength" 
              class="chat-textarea overflow-hidden transition w-full outline-0 !mt-0.75 !min-h-max {{ $attributes->get('class') }}" 
              placeholder="{{ $placeholder }}"
              {{ $attributes }}
          ></textarea>
        </div>
        
        <div class="top-0 right-0 text-gray @if($author) w-full md:w-auto flex justify-between items-center md:!block @endif">
            @if($author)
              <div class="!text-second block md:hidden">{{ $author }}</div>
            @endif

            <div class="flex justify-center items-start gap-1 sm:!gap-2">
                <div class="!text-xs sm:text-base p-1 rounded bg-white">
                    <span x-text="value.length"></span>/<span x-text="maxLength"></span>
                </div>

                @if($emoji)
                  <div class="emoji-btn relative hover:cursor-pointer p-1 !bg-white rounded transition hover:text-black"
                      data-target="{{ $id }}">
                      <!-- Emoji SVG icon here -->
                  </div>
                @endif

                @if($button)
                  <button class="p-1 !bg-white rounded transition hover:text-black">
                      @include('icons.arrow_right')
                  </button>
                @endif
            </div>
        </div>
    </div>

    @error($name)
      <div class="!mt-2 text-red-500">{{ $message }}</div>
    @enderror
</div>