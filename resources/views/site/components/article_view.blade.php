<div class="left_name_text">
    <h2>{{ $article->title }}</h2>
    <div class="theme_articles">
      <div class="talmaev">
        <div class="profile">
            <img src="{{ $article->author->avatar }}" alt="Avatar">
            <p>{{ $article->author->getName() }} <a
                    href="{{ url("/creators/{$article->author->profile}") }}">{{ $article->author->profile }}</a>
            </p>
        </div>
        <a href="{{ url("/creators/subscribe/{$article->author->profile}") }}" class="follow">{{ print_var('subscribe_button', $variables) }}</a>
      </div>
      <div class="block_date">
          <span>{{ \Illuminate\Support\Carbon::parse($article->created_at)->format('d.m.Y') }}</span>
          <span>{{ $article->views }} Views</span>
      </div>
      {!! $article->text !!}
    </div>
    <div class="follow_to_canal">
        @include('site.components.heading', ['title' => 'subscribe'])
        <a href="{{ url("/creators/subscribe/{$article->author->profile}") }}">{{ print_var('subscribe_button', $variables) }}</a>
    </div>
    <div class="bottom_group">
        <div class="tegs">
          @if (isset($article->tags) && $article->tags->isNotEmpty())
            @foreach ($article->tags as $tag)
                <a href="{{ url("/articles/tag/$tag->id") }}">{{ $tag->title }}</a>
            @endforeach
          @endif
        </div>
        <div class="share_group">
            @if ($article->likes_count > 0)
                <div class="avatar">
                    @foreach ($article->likes as $k => $like)
                      <img class="@if ($k > 0) last_img @endif"
                          src="{{ $like->author->avatar }}" alt="Avatar">
                    @endforeach
                </div>
            @endif
            <div class="like">
                <a href="{{ url("/articles/like/{$article->id}") }}">
                  @include('icons.like')
                </a>
                <p>Like</p>
                <span>{{ $article->likes_count }}</span>
            </div>
            <div class="connects">
                <a href="#" class="first_connect">
                  @include('icons.facebook-sm')
                </a>
                <a href="#" class="second_connect">
                  @include('icons.twitter-sm')
                </a>
                <a href="#" class="third_connect">
                  @include('icons.reddit-sm')
                </a>
                <a href="#" class="share">{{ print_var('share_message', $variables) }}</a>
            </div>
        </div>
    </div>
</div>
