@extends('layouts.site')

@section('content')
  <section class="home_tips relative">
    @include('site.components.parallax', ['class' => 'parallax-insights'])
    <div class="container relative z-20">
        <div class="about_block">
            @include('site.components.heading', ['variables' => $page->config->where(fn($item) => str_contains($item->name, 'page_'))])
            @include('site.components.breadcrumbs')
            {{-- @include('site.components.search') --}}

            <x-search placeholder="Search by keywords and tags..." />
        </div>
    </div>
  </section>
  <section class="tips_news_group articles-catalogue">
    <div class="container">
        <div class="about_block justify-betwee items-stretch !gap-12">
            <x-card size="sm" class="item_group basis-9/12 lg:basis-4/5">
                @php
                    $currentSort = $sortOption ?? request()->get('sort', 'rating');
                @endphp
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between !gap-4 !mb-10">
                  <x-title tag="h3" class="!font-normal !mb-0">Travel Insights</x-title>
                  <div class="flex items-center gap-2">
                    <label class="text-gray text-sm">Sort by:</label>
                    <x-form.select
                      name="insights-sort"
                      :compact="true"
                      :tooltip="false"
                      labelClass="!text-black"
                      value="{{ $currentSort }}"
                      :label="$currentSort === 'rating' ? 'Top Rated' : ($currentSort === 'popular' ? 'Most Popular' : ($currentSort === 'newest' ? 'Newest First' : 'Oldest First'))"
                      :options="[
                        'rating' => 'Top Rated',
                        'popular' => 'Most Popular',
                        'newest' => 'Newest First',
                        'oldest' => 'Oldest First',
                      ]"
                    />
                  </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 !gap-6 insights-articles">

                  @foreach($articles as $item)
                    <div class="cards_group">
                        <a href="{{ $item->makeFeedUrl() }}">
                          <img src="{{ $item->preview?->image }}" alt="Article {{ $item->id }}" class="main_img">
                        </a>
                        <a href="{{ $item->makeFeedUrl() }}">
                            <h3>{{ $item->title }}</h3>
                        </a>
                        <div class="print-content text-[#A4A0A0]">{!! $item->short() !!}</div>
                        <div class="date">
                            <span>{{ \Illuminate\Support\Carbon::parse($item->created_at)->format('d.m.Y') }}</span>
                            <div class="name_author">
                              <a class="group w-full flex items-center justify-start gap-2" href="{{ $item->author->makeProfileUrl() }}">
                                @php
                                  $avatar = $item->author->avatar ?? null;
                                  $avatarUrl = is_string($avatar) ? $avatar : (is_object($avatar) && method_exists($avatar, '__toString') ? (string)$avatar : '/storage/images/default_avatar.png');
                                  $avatarUrl = str_starts_with($avatarUrl, 'http') || str_starts_with($avatarUrl, '/') ? $avatarUrl : url($avatarUrl);
                                @endphp
                                <img src="{{ $avatarUrl }}" alt="Avatar {{ $item->author->name }}">
                                <p class="group-hover:!text-black transition">{{$item->author->profile }}</p>
                              </a>
                            </div>
                        </div>
                    </div>
                  @endforeach
								</div>

                {{-- @include('site.components.paginator', ['paginator' => $articles]) --}}
            </x-card>
            <div class="basis-3/12 lg:basis-1/5">
              <x-title class="!font-normal md:!text-[1.7rem] !mb-6">Travel News</x-title>
              @php
                $newsItems = $news->items();
                $newsNextPage = $news->hasMorePages() ? $news->currentPage() + 1 : null;
              @endphp

              <div
                class="travel-news-container flex flex-col gap-4"
                data-endpoint="{{ route('insights.news') }}"
                data-per-page="{{ $news->perPage() }}"
                data-next-page="{{ $newsNextPage ?? '' }}"
              >
                <div class="travel-news-list flex flex-col gap-4">
                  @include('site.pages.insights.partials.news-items', ['items' => $newsItems])
                </div>
                <div class="travel-news-loader text-center py-4 text-[#FC7361] hidden">
                  Loading more news...
                </div>
                
              </div>
            </div>
        </div>
    </div>
  </section>
@push('js')
    <script src="{{ asset('assets/js/insights.js') }}"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector('input[name="insights-sort"]');
        if (!input) return;

        const currentSort = '{{ $currentSort ?? "rating" }}';
        input.value = currentSort;

        // Sync Alpine select label once, then listen for user changes.
        setTimeout(() => {
          input.dispatchEvent(new Event('input', { bubbles: true }));

          setTimeout(() => {
            let timeoutId;
            let lastValue = input.value;
            input.addEventListener('input', function () {
              if (this.value === lastValue) return;
              lastValue = this.value;

              clearTimeout(timeoutId);
              timeoutId = setTimeout(() => {
                const url = new URL(window.location.href);
                const params = url.searchParams;
                const value = this.value;
                if (value === 'rating') {
                  params.delete('sort');
                } else {
                  params.set('sort', value);
                }
                url.search = params.toString();
                window.location.href = url.toString();
              }, 150);
            });
          }, 150);
        }, 80);
      });
    </script>
@endpush

@endsection
