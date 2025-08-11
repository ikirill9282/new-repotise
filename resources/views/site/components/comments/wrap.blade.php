@props([
  'type' => 'comment',
])

<div class="about_block">
    <div class="title_block">
        @include('site.components.heading', [
            'variables' => $variables->filter(fn($item) => str_contains($item->name, 'comment')),
        ])
        <span>{{ $model->countComments() }}</span>
    </div>
    <div
        class="reply-block italic flex justify-start items-stretch !p-4 pr-8 rounded-xl bg-sky-600/5 !mb-5 relative hidden">
        <div class="text-gray-400 rotate-180 !mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="25px" height="25px"
                viewBox="0 0 48 48" enable-background="new 0 0 48 48" id="Layer_5" version="1.1" xml:space="preserve">
                <g>
                    <polygon fill="currentColor"
                        points="15.706,15.353 15.706,11.251 0.021,23.45 15.706,35.649 15.706,31.548 5.294,23.45  " />
                    <path
                        d="M47.979,29.074c0-6.212-5.038-11.25-11.251-11.25h-0.001H25.479v-6.573L9.794,23.45l15.686,12.199v-6.575   h14.232c3.106,0,5.625,2.52,5.625,5.625c0,0.725-0.148,1.413-0.399,2.05C46.819,34.739,47.979,32.045,47.979,29.074z"
                        fill="currentColor" />
                </g>
            </svg>
        </div>
        <div class="reply-text grow"></div>
        <div class="text-gray-400 drop-reply absolute !p-4 top-50 right-0 translate-y-[-50%] hover:cursor-pointer"
            data-key="{{ \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $model->id]) }}">
            @include('icons.close')
        </div>
    </div>
    <div class="write_comment_group">
        @if (!auth()->check())
            <a href="#" class="go_comment open_auth">Login to comment</a>
        @else
          @if (isset($type) && ($type === 'review' && auth()->user()->canWriteReview($model)))
            <x-feedback :model="$model" :variables="$variables" type="review">
              <x-slot:head>
                @if(isset($stars) && $stars)
                  <div class="stars_filter w-full flex gap-2 justify-start items-center">
                      <input type="hidden" name="rating">
                      <span class="numbers">0</span>
                      <div class="stars">
                          <span data-value="1">
                              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 16 17"
                                  fill="none">
                                  <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                      stroke="#FFDB0C" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round">
                                  </path>
                              </svg>
                          </span>
                          <span data-value="2">
                              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 16 17"
                                  fill="none">
                                  <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                      stroke="#FFDB0C" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round">
                                  </path>
                              </svg>
                          </span>
                          <span data-value="3">
                              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 16 17"
                                  fill="none">
                                  <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                      stroke="#FFDB0C" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round">
                                  </path>
                              </svg>
                          </span>
                          <span data-value="4">
                              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 16 17"
                                  fill="none">
                                  <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                      stroke="#FFDB0C" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round">
                                  </path>
                              </svg>
                          </span>
                          <span data-value="5">
                              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 16 17"
                                  fill="none">
                                  <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                      stroke="#FFDB0C" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round">
                                  </path>
                              </svg>
                          </span>
                      </div>
                      <span class="numbers">5</span>
                  </div>
                  <div class="text-red-500 w-full hidden text-left" id="rating-error"></div>
                @endif
              </x-slot:head>
            </x-feedback>
          @elseif (isset($type) && $type == 'article' && auth()->user()->canWriteComment($model))
            <x-feedback :model="$model" :variables="$variables" type="article"></x-feedback>
          @endif
          <div class="text-red-500 w-full text-left hidden" id="text-error"></div>
        @endif
    </div>
    <div class="block_commends">
        @foreach ($model->comments as $comment)
            @include('site.components.comments.comment', ['comment' => $comment , 'type' => $type])
        @endforeach

        @if (isset($comments_count))
            <div class="more_commends_group">
                <a href="#">{{ print_var('comment_more_comments', $variables) }} (50 of
                    {{ $comments_count }})</a>
            </div>
        @endif
    </div>
</div>
