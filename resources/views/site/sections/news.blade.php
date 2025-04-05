@php
$news = \App\Models\News::getLastNews(5);
@endphp

<section class="news">
  <div class="container">
      <div class="about_block">
          @include('site.components.heading', ['variables' => $variables])
          <div class="items_news !items-stretch">
              @foreach ($news as $news_item)
                <div class="item justify-between">
                    <a href="{{ url("/news/$news_item->slug") }}">
                      <p>{{ $news_item->title }}</p>
                    </a>
                    <a href="{{ url("/news/$news_item->slug") }}">
                      <img src="{{ $news_item->preview->image }}" alt="News image {{ $news_item->id }}">
                    </a>
                </div>
              @endforeach
          </div>
          
          <a href="{{ print_var('more_link', $variables) }}" class="look_more">
            {{ print_var('more_text', $variables) }}
          </a>
      </div>
  </div>
</section>