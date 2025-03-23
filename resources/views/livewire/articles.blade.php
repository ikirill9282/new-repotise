@php
    $items = collect($articles->items());
    $steps = intval(floor(9 / $items->count()));
    $step = 0;

    while ($step <= $steps) {
      $items = $items->merge(collect($articles->items()));
      $step++;
    }
    $items = $items->slice(0, 9);
@endphp
<div>
    <section class="home_tips">
        <div class="container">
            <div class="about_block">
                @include('site.components.heading', ['variables' => $variables])
                @include('site.components.breadcrumbs')
                <input type="search" placeholder="{{ $variables->get('search_text')?->value }}">
            </div>
        </div>
    </section>
    <section class="tips_news_group">
        <div class="container">
            <div class="about_block">
                <div class="item_group">
                    <div class="row">
                        @foreach ($items as $item)
                          <div class="col-lg-4 col-md-6">
                              <div class="cards_group">
                                  <a href="{{ $item->makeInsightsUrl() }}">
                                    <img src="{{ $item->preview->image }}" alt="Article Preview" class="main_img">
                                  </a>
                                  <a href="{{ $item->makeInsightsUrl() }}">
                                      <h3>{{ $item->title }}</h3>
                                  </a>
                                  <p>{!! $item->short() !!}</p>
                                  <div class="date">
                                      <span>{{ \Illuminate\Support\Carbon::parse($item->created_at)->format('d.m.Y') }}</span>
                                      <div class="name_author">
                                          <img src="{{ $item->author->avatar }}" alt="Avatar">
                                          <p>Автор {{ ucfirst($item->author->username) }} </p>
                                      </div>
                                  </div>
                              </div>
                          </div>
                        @endforeach
                    </div>
                    {{ $articles->links() }}
                  </div>
                  @include('site.components.last_news', ['news' => $last_news])
            </div>
        </div>
    </section>
</div>
