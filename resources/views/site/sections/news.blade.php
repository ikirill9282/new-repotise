@php
$news = \App\Models\Article::getLastNews(5);
@endphp

<section class="news">
  <div class="container !mx-auto">
      <div class="about_block">
          @include('site.components.heading', ['variables' => $variables])
          <div class="items_news">
              {{-- <div class="flex w-max md:w-full md:!grid grid-cols-4 lg:grid-cols-5 gap-4"> --}}

                <div class="swiper" id="swiper-news">
                  <div class="swiper-wrapper">
                    @foreach ($news as $news_item)
                      <div class="swiper-slide !h-auto">
                        <div class="item !h-full !w-full flex flex-col justify-between !max-w-none">
                            <a href="{{ $news_item->makeFeedUrl() }}">
                              <p>{{ $news_item->title }}</p>
                            </a>
                            <a href="{{ $news_item->makeFeedUrl() }}">
                              <img class="!w-full" src="{{ $news_item->preview->image }}" alt="News image {{ $news_item->id }}">
                            </a>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
            {{-- </div> --}}
          </div>
          
          <a href="{{ print_var('more_link', $variables) }}" class="look_more">
            {{ print_var('more_text', $variables) }}
          </a>
      </div>
  </div>
</section>