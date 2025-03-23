<div class="commends_group @if(isset($class)) {{ $class }} @endif">
  <div class="commend">
    <img src="{{ $comment['author']['avatar'] }}" alt="Avatar"
        class="img_commendor">
    <div class="right_text_group">
        <div class="name_commendor">
            <div class="left_text">
                <a href="#">@talmaev1</a>
                <span>{{ $comment['author']['username'] }}</span>
            </div>
        </div>
        <div class="date">
            <span>{{ \Illuminate\Support\Carbon::parse($comment['created_at'])->format('d.m.Y') }}</span>
        </div>
        <div class="review">
            <p>
                {!! $comment['text'] !!}
            </p>
        </div>
        <div class="likes">
            <div class="left_groups_like">
                @if (isset($comment['likes']) && !empty($comment['likes']))
                    <div class="img_men">
                        @foreach ($comment['likes'] as $like)
                            <img src="{{ isset($like['author']['avatar']) ? url($like['author']['avatar']) : '' }}"
                                alt="Avatar">
                        @endforeach
                    </div>
                @endif
                @include('icons.like_comment', [
                    'count' => $comment['likes_count'],
                ])
            </div>
            <div class="right_edit">
                <a href="#">Edit</a>
                <a href="#">Delete</a>
            </div>
        </div>
    </div>
  </div>
  @if (array_key_exists('children', $comment) && is_array($comment['children']))
    @foreach($comment['children'] as $child)
      @include('site.components.comment', ['comment' => $child, 'class' => 'answers'])
    @endforeach
  @endif
</div>