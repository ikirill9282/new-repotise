<div class="commend">
  
  <img src="{{ $comment['author']['options']['avatar'] }}" alt="Avatar" class="img_commendor">


  <div class="right_text_group">
      <div class="name_commendor">
          <div class="left_text flex-row items-center gap-2">
              <a href="{{ url('/profile/@' . $comment['author']['username']) }}">{{ $comment['author']['name'] }}</a>
              @if(isset($comment['rating']))
                <div class="flex">
                   @for($i = 0; $i < $comment['rating']; $i++)
                      <svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 16 17"
                          fill="#FFDB0C">
                          <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                              stroke="#FFDB0C" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round">
                          </path>
                      </svg>
                   @endfor
                </div>
              @endif
          </div>
          <a href="#" class="editor_btn" data-target="editor-{{ $comment['id'] }}">
              <img src="{{ asset('assets/img/options.svg') }}" alt="Options">
          </a>
          @if (auth()->check())
              <x-comment_menu :variables="$variables" :id="$comment['id']" :user_id="$comment['user_id']"></x-comment_menu>
              {{-- <div class="right_edit h-0 transition overflow-hidden" id="editor-{{ $comment['id'] }}"
                  data-comment="{{ $comment['id'] }}">
                  <a href="#">{{ print_var('comment_report_message', $variables) }}</a>

                  @if (auth()->user()->id == $comment['user_id'] || auth()->user()->hasRole('admin'))
                      <a href="#">{{ print_var('comment_edit_message', $variables) }}</a>
                  @endif

                  @if (auth()->user()->hasRole('admin'))
                      <a href="#">{{ print_var('comment_delete_message', $variables) }}</a>
                  @endif
              </div> --}}
          @endif
      </div>
      <div class="date">
          <span>{{ \Illuminate\Support\Carbon::parse($comment['created_at'])->format('d.m.Y') }}</span>
      </div>
      <div class="review">
          <p>
              {{ $comment['text'] }}
          </p>
      </div>
      <div class="likes">
          <div class="left_groups_like">
              <span class="comment-link show-more hidden">
                {{ print_var('comment_more_message', $variables) }}
              </span>
              <a 
                href="#" 
                class="comment-link for_answer reply-button {{ auth()->check() ? '' : 'open_auth' }}"
                data-reply="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $comment['id']]) }}"
                >
                  {{ print_var('comment_reply_message', $variables) }}
              </a>

              @if (isset($comment['likes']) && !empty($comment['likes']))
                  <div class="img_men">
                      @foreach ($comment['likes'] as $key => $like)
                          <img src="{{ isset($like['author']['options']['avatar']) ? url($like['author']['options']['avatar']) : '' }}"
                              class="@if ($key > 0) last_img @endif rounded-full" alt="Avatar">
                      @endforeach
                  </div>
              @endif

              @php
                $hash_id = \App\Helpers\CustomEncrypt::generateUrlHash([$comment['id']]);
              @endphp
              <div class="like_commend {{ auth()->check() ? '' : 'open_auth' }}">
                  <a 
                    href="/feedback/likes" 
                    class="like_to_commend feedback_button {{ is_liked((isset($type) ? $type : 'comment'), $comment['id']) ? 'liked' : '' }}" 
                    data-item="{{ hash_like((isset($type) ? $type : 'comment'), $comment['id']) }}" 
                    data-id="{{ $hash_id }}"
                  >
                    @include('icons.like_comment')
                  </a>
                  <span data-counter="{{ $hash_id }}">{{ $comment['likes_count'] }}</span>
              </div>
          </div>
      </div>

      @if (enable_more($comment))
        <div class="more_answers mt-4">
            <a href="#" class="replies-button" data-item="{{ hash_more($comment) }}">
              {{ print_var('comment_show_replies', $variables) }} ({{ ($comment['children_count'] < 50) ? $comment['children_count'] : 50 }} of {{ $comment['children_count'] }})
            </a>
        </div>
      @endif
  </div>
</div>
