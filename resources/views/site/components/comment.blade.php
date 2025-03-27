<div class="commends_group @if (isset($class)) {{ $class }} @endif">
    <div class="commend">
        <img src="{{ $comment['author']['avatar'] }}" alt="Avatar" class="img_commendor">
        <div class="right_text_group">
            <div class="name_commendor">
                <div class="left_text">
                    <a href="#">@talmaev1</a>
                </div>
                <a href="#"><img src="{{ asset('assets/img/options.svg') }}" alt="Options"></a>
            </div>
            <div class="date">
                <span>{{ \Illuminate\Support\Carbon::parse($comment['created_at'])->format('d.m.Y') }}</span>
            </div>
            <div class="review">
                <p>
                  {!! $comment['text'] !!}
                </p>
                <span class="show-more" onclick="toggleText()">More</span>
            </div>
            <div class="likes">
                <div class="left_groups_like">
                  <a href="#" class="for_answer">Reply</a>
                  @if (isset($comment['likes']) && !empty($comment['likes']))
                    <div class="img_men">
                      @foreach ($comment['likes'] as $key => $like)
                        <img src="{{ isset($like['author']['avatar']) ? url($like['author']['avatar']) : '' }}"
                          class="@if($key > 0) last_img @endif"
                          alt="Avatar">
                      @endforeach
                    </div>
                  @endif
                    <div class="like_commend">
                        <a href="#" class="like_to_commend">
                          @include('icons.like_comment')
                        </a>
                        <span>{{ $comment['likes_count'] }}</span>
                    </div>
                </div>
            </div>
            <div class="right_edit">
                <a href="#">Report</a>
                <a href="#">Edit</a>
                <a href="#">Delete</a>
            </div>
        </div>
    </div>

    @if (array_key_exists('children', $comment) && is_array($comment['children']))
        @foreach ($comment['children'] as $child)
            @include('site.components.comment', ['comment' => $child, 'class' => 'answers border_none_block', 'replies' => false])
        @endforeach
    @endif

    @if(!isset($replies))
      <div class="more_answers">
        <a href="#">Show More Replies (50 of 248)</a>
      </div>
    @endif
</div>
