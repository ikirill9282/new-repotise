<div class="last_news">
  <{{ $variables->get('last_news_heading')?->value ?? 'h3' }}>
    {{ $variables->get('last_news_title')?->value ?? 'Travel Insights 123' }}
  </{{ $variables->get('last_news_heading')?->value ?? 'h3' }}>

  @if (isset($news))
    @foreach ($news as $news_item)
      <div class="news_group">
          <a href="{{ url("$news_item->slug?nid=$news_item->id") }}">
              <p>{{ $news_item->title }}</p>
          </a>
          <a href="{{ url("$news_item->slug?nid=$news_item->id") }}"><img src="{{ $news_item->preview?->image }}" alt="News Previews"></a>
      </div>
    @endforeach
  @endif
</div>