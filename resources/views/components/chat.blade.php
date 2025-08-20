@props([
    'title' => 'Reviews',
    'type' => 'comment',
    'model' => null,
    'variables' => collect([]),
])

@php
  $can_write = ($type == 'review') ? auth()->user()?->canWriteReview($model) : auth()->user()?->canWriteComment($model);
  $message_exists = ($type == 'review' ) ? auth()->user()?->reviews()->where('product_id', $model->id)->exists() : true;
  $count = ($type == 'review') ? $model->reviews_count : $model->messages()->count();
@endphp

<div class="bg-light">
    <div class="container">
        <div class="chat w-full bg-white !px-0 !py-6 sm:!p-6 lg:!p-7 rounded-xl">
            <h2 class="font-bold sm:text-xl flex justify-start items-end gap-3 mb-0 sm:mb-4 !p-2 sm:!p-3 sm:!p-0">
                <span>{{ $title }}</span>
                <span class="text-gray sm:text-3xl">{{ $count }}</span>
            </h2>

            <div class="w-full py-2 !px-3 sm:!px-0 text-end" 
                x-data="messenger()"
                @input="checkInput($event.target)"
              >

                @if (!auth()->check())
                    <a @click.prevent="$dispatch('openModal', {modalName: 'auth'})" href="#"
                        class="!text-gray hover:!text-active border-b border-dashed pb-0.5 inline-block !mb-4 transition">Login
                        to review</a>
                @elseif ( 
                        $can_write||
                        auth()->user()->id == $model->author->id ||
                        auth()->user()->hasRole(['admin', 'super-admin']) ||
                        $message_exists
                      )

                    <form action="" class="feedback-form mb-4" data-type="{{ $type }}">
                        <input type="hidden" name="reply" value="" class="reply-input">
                        <input type="hidden" name="edit" value="" class="edit-input">

                        <input type="hidden" name="model"
                            value="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $model->id]) }}">

                        <div class="reply-block italic text-left flex justify-start items-stretch !p-4 pr-8 rounded-xl bg-sky-600/5 !mb-5 relative hidden">
                            <div class="text-gray-400 rotate-180 !mr-4 h-full flex justify-start items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="25px" height="25px" viewBox="0 0 48 48" enable-background="new 0 0 48 48"
                                    id="Layer_5" version="1.1" xml:space="preserve">
                                    <g>
                                        <polygon fill="currentColor"
                                            points="15.706,15.353 15.706,11.251 0.021,23.45 15.706,35.649 15.706,31.548 5.294,23.45  ">
                                        </polygon>
                                        <path
                                            d="M47.979,29.074c0-6.212-5.038-11.25-11.251-11.25h-0.001H25.479v-6.573L9.794,23.45l15.686,12.199v-6.575   h14.232c3.106,0,5.625,2.52,5.625,5.625c0,0.725-0.148,1.413-0.399,2.05C46.819,34.739,47.979,32.045,47.979,29.074z"
                                            fill="currentColor"></path>
                                    </g>
                                </svg>
                            </div>
                            <div class="reply-text grow"></div>
                            <div class="drop-reply text-gray-400 drop-reply absolute !p-4 top-0 right-0 hover:cursor-pointer"
                                data-key="aWQ9MzA">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 14 14" fill="none">
                                    <path
                                        d="M1 1C5.68629 5.68629 8.31371 8.31371 13 13M1 13C5.68629 8.31371 8.31371 5.68629 13 1"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                    <script xmlns=""></script>
                                </svg>
                            </div>
                        </div>
                            
                        @if ($can_write && $type == 'review')
                            <div class="stars_filter w-full flex gap-2 justify-start items-center mb-2">
                                <input type="hidden" name="rating">
                                <span class="numbers">0</span>
                                <div class="stars flex text-transparent">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span data-value="{{ $i }}">
                                            @include('icons.star')
                                        </span>
                                    @endfor
                                </div>
                                <span class="numbers">5</span>
                            </div>
                            <div class="text-red-500 w-full hidden text-left" id="rating-error"></div>
                        @endif


                        @if ($can_write || $type == 'comment')
                          <div class="block sm:hidden text-left mb-2">{{ auth()->user()->profile }}</div>
                        @endif
                        <div class="w-full flex justify-start items-start gap-2 sm:gap-3">
                            <div
                                class="relative grow flex items-center justify-start gap-2 sm:gap-3 bg-light rounded-lg !pl-3 py-3 sm:!ps-3 !pe-20 sm:!pe-2">
                                
                                @if (!$can_write || $type == 'comment')
                                  <div class="hidden sm:block">{{ auth()->user()->profile }}</div>
                                @endif
                                
                                @php
                                  $emoji_hash = \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $model->id])
                                @endphp
                                <textarea name="text" id="{{ $emoji_hash }}" rows="1"
                                    class="chat-textarea transition w-full !text-xs sm:!text-base leading-normal outline-0"
                                    placeholder="{{ $type == 'review' ? 'Write your review...' : 'Write your comment...'  }}"></textarea>

                                <div class="absolute top-0 right-0 text-gray">
                                    <div class="flex justify-center items-center py-3 px-2 sm:!px-3 gap-1 sm:!gap-2">
                                        <div class="!text-xs sm:text-base p-1 rounded bg-white">
                                            <span x-text="symbols"></span>/1000
                                        </div>
                                        <div class="emoji-btn hover:cursor-pointer p-1 !bg-white rounded transition hover:text-black"
                                            data-target="{{ $emoji_hash }}">
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

                                        @if (!$can_write || $type == 'comment')
                                            <button class="p-1 !bg-white rounded transition hover:text-black">
                                                @include('icons.arrow_right')
                                            </button>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            @if ($can_write && $type == 'review')
                                <button
                                    class="!p-2 sm:!p-4 flex flex-col sm:block bg-active hover:bg-secondary transition rounded text-white !text-xs sm:!text-base">
                                    <span>Post</span>
                                    <span>Review</span>
                                </button>
                            @endif
                        </div>

                        <div class="text-red-500 w-full hidden text-left mt-2" id="text-error"></div>
                        <div class="text-red-500 w-full hidden text-left mt-2" id="model-error"></div>
                    </form>
                @endif

                <div class="border-t border-gray/50 text-left text-base">
                    @foreach ($model->messagesType('parent')->getMessages() as $message)
                        <x-chat.message 
                          :message="$message" 
                          :author_id="$model->author->id" 
                          :resource="$type"
                        >
                        </x-chat.message>
                    @endforeach

                    @if ($model->getUnloadedMessagesCount() > 0)
                        <x-chat.more 
                          :resource="\App\Helpers\CustomEncrypt::generateUrlHash([
                            'id' => $model->id,
                            'offset' => $model->getLoadedMessagesCount(),
                            'type' => is_null($model->parent_id) ? 'parent' : 'child',
                            'resource' => $type,
                            'level' => 1,
                          ])" 
                          :offset="$model->getLoadedMessagesCount()" class="w-full text-center"
                          >
                            Show More Reviews ({{ $model->getLoadingMessagesCount() }} of
                            {{ $model->getUnloadedMessagesCount() }})
                        </x-chat.more>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        function messenger() {
            return {
                symbols: 0,
                emoji: null,
                setSymbols(value) {
                    this.symbols = value;
                },
                checkInput(elem) {
                    elem.style.height = 'auto';
                    elem.style.height = elem.scrollHeight + 'px';
                    this.setSymbols(elem.value.length);
                },
            }
        }

        $(document).ready(function() {
            
        });
    </script>
@endpush
