<div class="commends_group @if (isset($class)) {{ $class }} @endif">
    <div class="commend">
        <img src="{{ $comment['author']['avatar'] }}" alt="Avatar" class="img_commendor">
        <div class="right_text_group">
            <div class="name_commendor">
                <div class="left_text">
                    <a
                        href="{{ url('/users/profile/' . $comment['author']['profile']) }}">{{ $comment['author']['profile'] }}</a>
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
                    <span class="comment-link show-more">
                        {{ print_var('comment_more_message', $variables) }}
                    </span>
                    <a 
                      href="#" 
                      class="comment-link for_answer reply-button {{ auth()->check() ? '' : 'open_auth' }}"
                      >
                        {{ print_var('comment_reply_message', $variables) }}
                    </a>

                    @if (isset($comment['likes']) && !empty($comment['likes']))
                        <div class="img_men">
                            @foreach ($comment['likes'] as $key => $like)
                                <img src="{{ isset($like['author']['avatar']) ? url($like['author']['avatar']) : '' }}"
                                    class="@if ($key > 0) last_img @endif" alt="Avatar">
                            @endforeach
                        </div>
                    @endif

                    <div class="like_commend {{ auth()->check() ? '' : 'open_auth' }}">
                        <a href="#" class="like_to_commend">
                            @include('icons.like_comment')
                        </a>
                        <span>{{ $comment['likes_count'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (array_key_exists('children', $comment) && is_array($comment['children']))
        @foreach ($comment['children'] as $child)
            @include('site.components.comment', [
                'comment' => $child,
                'class' => 'answers border_none_block',
                'replies' => false,
            ])
        @endforeach
    @endif

    @if (!isset($replies))
        <div class="more_answers">
            <a href="#">{{ print_var('comment_show_replies', $variables) }} (50 of 248)</a>
        </div>
    @endif
</div>
