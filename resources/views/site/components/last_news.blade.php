<div class="last_news">
  @include('site.components.heading', ['title' => 'last_news'])
  <div class="block_news_items">
    @if (isset($news))
    <div class="swiper" id="last_news_swiper">
      <div class="swiper-wrapper">
        @foreach ($news as $news_item)
          <div class="swiper-slide">
            <div class="news_group">
                <a href="{{ url("$news_item->slug?nid=$news_item->id") }}">
                    <p>{{ $news_item->title }}</p>
                </a>
                <a href="{{ url("$news_item->slug?nid=$news_item->id") }}">
                  <img src="{{ $news_item->preview?->image }}" alt="News preview {{ $news_item->id }}">
                </a>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>