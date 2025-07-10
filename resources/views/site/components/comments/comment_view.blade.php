<div class="commend">
  <img src="{{ $comment['author']['options']['avatar'] }}" alt="Avatar" class="img_commendor">
  <div class="right_text_group">
      <div class="name_commendor">
          <div class="left_text">
              <a
                  href="{{ url('/profile/@' . $comment['author']['username']) }}">{{ $comment['author']['name'] }}</a>
          </div>
          <a href="#" class="editor_btn" data-target="editor-{{ $comment['id'] }}">
              <img src="{{ asset('assets/img/options.svg') }}" alt="Options">
          </a>
          @if (auth()->check())
              <div class="right_edit h-0 transition overflow-hidden" id="editor-{{ $comment['id'] }}"
                  data-comment="{{ $comment['id'] }}">
                  <a href="#">{{ print_var('comment_report_message', $variables) }}</a>

                  @if (auth()->user()->id == $comment['user_id'] || auth()->user()->hasRole('admin'))
                      <a href="#">{{ print_var('comment_edit_message', $variables) }}</a>
                  @endif

                  @if (auth()->user()->hasRole('admin'))
                      <a href="#">{{ print_var('comment_delete_message', $variables) }}</a>
                  @endif
              </div>
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
