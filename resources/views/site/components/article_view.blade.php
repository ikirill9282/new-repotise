<div class="left_name_text">
    @php
      $group = \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $article->author->id]);
      $resource = \Illuminate\Support\Facades\Crypt::encrypt($article->author->id);
    @endphp
    <h2>{{ $article->title }}</h2>
    <div class="theme_articles">
      <div class="talmaev">
        <div class="profile">
            <img src="{{ $article->author->avatar }}" alt="Avatar">
            <p>
              <span>{{ $article->author->name }}</span>
              <a class="author-link !text-md" href="{{ $article->author->makeProfileUrl() }}">{{ $article->author->profile }}</a>
            </p>
        </div>
        <a 
          href="{{ $article->author->makeSubscribeUrl() }}" 
          class="follow follow-btn {{ auth()->check() ? '' : 'open_auth' }}"
          data-resource="{{ $resource }}"
          data-group="{{ $group }}"
        >
          {{ $article->author->hasFollower(auth()->user()?->id) ? 'Unsubscribe' : 'Subscribe' }}
        </a>
      </div>
      <div class="block_date">
          <span>{{ \Illuminate\Support\Carbon::parse($article->created_at)->format('d.m.Y') }}</span>
          <span>{{ $article->views }} Views</span>
      </div>
      {!! $article->text !!}
    </div>
    <div class="follow_to_canal">
        @include('site.components.heading', ['title' => 'subscribe'])
        <a 
          href="{{ $article->author->makeSubscribeUrl() }}"
          class="follow-btn {{ auth()->check() ? '' : 'open_auth' }}"
          data-resource="{{ $resource }}"
          data-group="{{ $group }}"
        >
          {{ $article->author->hasFollower(auth()->user()?->id) ? 'Unsubscribe' : 'Subscribe' }}
        </a>
    </div>
    <div class="bottom_group">
        <div class="tegs">
          @if (isset($article->tags) && $article->tags->isNotEmpty())
            @foreach ($article->tags as $tag)
                <a href="{{ url("/search?q=$tag->title") }}">{{ $tag->title }}</a>
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
            @php
              $hash_id = \App\Helpers\CustomEncrypt::generateUrlHash([$article->id]);
            @endphp

            <div class="like">
                <a 
                  href="/feedback/likes"
                  class="feedback_button {{ auth()->check() ? '' : 'open_auth' }} {{ is_liked('article', $article->id) ? 'liked' : '' }}"
                  data-item="{{ hash_like('article', $article->id) }}"
                  data-id="{{ $hash_id }}"
                >
                  @include('icons.like')
                </a>
                <p>Like</p>
                <span data-counter="{{ $hash_id }}">
                  {{ $article->likes_count }}
                </span>
            </div>
            <div class="connects">
                <a href="#" class="first_connect hover:!text-blue-500">
                  @include('icons.facebook-sm')
                </a>
                <a href="#" class="second_connect hover:!text-black">
                  @include('icons.twitter-sm')
                </a>
                <a href="#" class="third_connect hover:!text-black">
                  @include('icons.reddit-sm')
                </a>
                <a href="#" class="share">{{ print_var('share_message', $variables) }}</a>
            </div>
        </div>
    </div>
</div>
