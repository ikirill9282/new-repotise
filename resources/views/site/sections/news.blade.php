@php
$news = $variables->get('news_ids')->value;
$news = \App\Models\News::whereIn('id', $news)->get();
$news_item = $news->first();
@endphp
<section class="news">
  <div class="container">
      <div class="about_block">
          <h2>Новости</h2>
          <div class="items_news">
            @for($i = 0; $i < 5; $i++)
              <div class="item">
                  <a href="{{ url("/news/$news_item->slug") }}">
                      <p>{{ $news_item->title }}</p>
                  </a>
                  <a href="{{ url("/news/$news_item->slug") }}"><img src="{{ url($news_item->preview->image) }}" alt="News Preview"></a>
              </div>
            @endfor
          </div>
          <a href="{{ $variables->get('more_link')->value }}" class="look_more">{{ $variables->get('more_text')->value }}</a>
      </div>
  </div>
</section>