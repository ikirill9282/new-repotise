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
          message: '{{ $default }}',
          max: {{ $max }},
          getSymbols() {
              $refs.ta.style.height = 'auto';

              if ($refs.ta.scrollHeight > 24) {
                $refs.ta.style.height = $refs.ta.scrollHeight + 'px';
              }
      
              return this.message.length;
          },
          setMessage(val) {
              this.message = val;
          }
        }" 
        x-init="$watch('message', (value) => {
          if (value.length > max) {
            setMessage(value.slice(0, max));
          }
        });"
        class="relative w-full flex items-end md:items-start justify-start grow
              flex-col-reverse md:flex-row gap-2 sm:gap-3
              md:bg-light md:rounded-lg md:p-3
              "
        >
        @if($author)
          <div class="hidden md:block">{{ $author }}</div>
        @endif

        <div class="bg-light w-full !grow rounded-lg !p-3 md:!p-0 !leading-0">
          <textarea name="{{ $name }}" rows="1" x-ref="ta" x-model="message" id="{{ $id }}" {{ $attributes }}
              class="chat-textarea overflow-hidden transition w-full outline-0 !mt-0.75 {{ $attributes->get('class') }}" placeholder="{{ $placeholder }}"></textarea>
        </div>
        
        <div class="top-0 right-0 text-gray @if($author) w-full md:w-auto flex justify-between items-center md:!block @endif">
            @if($author)
              <div class="!text-second block md:hidden">{{ $author }}</div>
            @endif

            <div class="flex justify-center items-start gap-1 sm:!gap-2">
                <div class="!text-xs sm:text-base p-1 rounded bg-white">
                    <span x-text="getSymbols"></span>/<span x-text="max"></span>
                </div>

                @if($emoji)
                  <div class="emoji-btn relative hover:cursor-pointer p-1 !bg-white rounded transition hover:text-black"
                      data-target="{{ $id }}">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 16 16"
                          fill="none">
                          <g clip-path="url(#clip0_1731_30973)">
                              <path
                                  d="M11.6667 7.33333C11.6033 7.33333 11.5393 7.31533 11.482 7.27733L9.482 5.944C9.38933 5.882 9.33333 5.778 9.33333 5.66667C9.33333 5.55533 9.38867 5.45133 9.482 5.38933L11.482 4.056C11.6353 3.954 11.842 3.99533 11.944 4.14867C12.046 4.302 12.0047 4.50867 11.8513 4.61067L10.2673 5.66667L11.8513 6.72267C12.0047 6.82467 12.046 7.03133 11.944 7.18467C11.88 7.28133 11.7747 7.33333 11.6667 7.33333ZM4.51867 7.27733L6.51867 5.944C6.61133 5.882 6.66733 5.778 6.66733 5.66667C6.66733 5.55533 6.612 5.45133 6.51867 5.38933L4.51867 4.056C4.36467 3.954 4.158 3.99533 4.05667 4.14867C3.95467 4.302 3.996 4.50867 4.14933 4.61067L5.73333 5.66667L4.14933 6.72267C3.996 6.82467 3.95467 7.03133 4.05667 7.18467C4.12067 7.28133 4.22667 7.33333 4.33467 7.33333C4.398 7.33333 4.46133 7.31533 4.51867 7.27733ZM11.974 10.0167C12.0473 9.668 11.9667 9.31533 11.752 9.05067C11.5513 8.80333 11.2633 8.66733 10.94 8.66733H5.058C4.73467 8.66733 4.446 8.80333 4.24533 9.052C4.03067 9.31867 3.95133 9.67333 4.02733 10.024C4.376 11.628 5.88867 13.3333 8.00333 13.3333C10.118 13.3333 11.6327 11.6247 11.9727 10.0167H11.974ZM10.9407 9.334C11.092 9.334 11.184 9.408 11.2347 9.47067C11.3207 9.57733 11.3533 9.73 11.322 9.87867C11.036 11.23 9.77667 12.6667 8.00467 12.6667C6.23267 12.6667 4.97333 11.232 4.68 9.882C4.64733 9.73133 4.68 9.57733 4.76533 9.47067C4.816 9.408 4.90733 9.33333 5.05867 9.33333H10.9407V9.334ZM16.0007 8.00067C16 3.58867 12.4113 0 8 0C3.58867 0 0 3.58867 0 8C0 12.4113 3.58867 16 8 16C12.4113 16 16 12.4113 16 8L16.0007 8.00067ZM15.334 8.00067C15.334 12.044 12.044 15.334 8.00067 15.334C3.95733 15.334 0.666667 12.0433 0.666667 8C0.666667 3.95667 3.95667 0.666667 8 0.666667C12.0433 0.666667 15.3333 3.95667 15.3333 8L15.334 8.00067Z"
                                  fill="currentColor"></path>
                          </g>
                          <defs>
                              <clipPath id="clip0_1731_30973">
                                  <rect width="16" height="16" fill="white"></rect>
                              </clipPath>
                          </defs>
                      </svg>
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
</div>
