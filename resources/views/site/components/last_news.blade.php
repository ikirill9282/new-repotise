<div class="last_news">
  <{{ $variables->get('last_news_heading')?->value ?? 'h3' }}>
    {{ $variables->get('last_news_title')?->value ?? 'Travel Insights' }}
  </{{ $variables->get('last_news_heading')?->value ?? 'h3' }}>
  <div class="block_news_items">
    @if (isset($news))
      @foreach ($news as $news_item)
        <div class="news_group">
            <a href="{{ url("$news_item->slug?nid=$news_item->id") }}">
                <p>{{ $news_item->title }}</p>
            </a>
            <a href="{{ url("$news_item->slug?nid=$news_item->id") }}">
              <img src="{{ $news_item->preview?->image }}" alt="News preview {{ $news_item->id }}">
            </a>
        </div>
      @endforeach
    @endif
  </div>
</div>