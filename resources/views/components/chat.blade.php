@props([
    'title' => 'Reviews',
    'type' => 'comment',
    'model' => null,
    'variables' => collect([]),
])

@php
  $can_write = ($type == 'review') ? auth()->user()?->canWriteReview($model) : auth()->user()?->canWriteComment($model);
  $message_exists = ($type == 'review' ) ? auth()->user()?->reviews()->where('product_id', $model->id)->exists() : true;
  $count = ($type == 'review') ? $model->reviews_count : $model->messages()->whereNull('parent_id')->count();
@endphp

<div class="bg-light" id="review">
    <div class="container">
        <div class="chat w-full bg-white !px-0 !py-6 sm:!p-6 lg:!p-7 rounded-xl">
            <h2 class="font-bold sm:text-xl flex justify-start items-end gap-3 !mb-4 md:!mb-8">
                <span>{{ $title }}</span>
                <span class="text-gray sm:text-3xl">{{ $count }}</span>
            </h2>

            <div class="w-full py-2 !px-3 sm:!px-0 text-end" 
                x-data="messenger()"
                @input="checkInput($event.target)"
              >

                @if (!auth()->check())
                  <a 
                      @click.prevent="$dispatch('openModal', {modalName: 'auth'})" href="#"
                      class="!text-gray hover:!text-active border-b border-dashed pb-0.5 inline-block !mb-4 transition"
                    >
                    Login to review
                  </a>
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


                        <div class="w-full flex justify-start items-start gap-2 sm:gap-3">
                            @php
                              $emoji_hash = \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $model->id])
                            @endphp

                            @php
                              $placeholder = ($type === 'review') ? 'Add a Review...' : 'Add a comment...';
                            @endphp
                            <x-form.text-counter 
                              max="1000" 
                              :emoji="true" 
                              :id="$emoji_hash"
                              :author="(!$can_write || $type == 'comment') ? auth()->user()->profile : null"
                              placeholder="{{ $placeholder }}"
                            ></x-form.text-counter>
                            
                            @if ($can_write && $type == 'review')
                                <x-btn
                                    class="!p-2 sm:!p-2.75 flex flex-col !w-auto bg-active hover:bg-secondary transition rounded text-nowrap text-white !text-xs sm:!text-base">
                                    <span>Post</span>
                                    <span>Review</span>
                                </x-btn>
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
