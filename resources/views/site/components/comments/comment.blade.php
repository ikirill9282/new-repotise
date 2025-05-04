<div class="commends_group @if (isset($class)) {{ $class }} @endif">
  
    @include('site.components.comments.comment_view')

    @if (array_key_exists('children', $comment) && is_array($comment['children']))
        @foreach ($comment['children'] as $child)
            @include('site.components.comments.comment', [
                'comment' => $child,
                'class' => 'answers border_none_block',
                'replies' => false,
            ])
        @endforeach
    @endif
</div>
