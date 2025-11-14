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
                    <label for="insights-sort" class="text-gray text-sm">Sort by:</label>
                    <select
                      class="tg-select"
                      id="insights-sort"
                      x-data="{}"
                      x-on:change="(evt) => {
                        const url = new URL(window.location.href);
                        const params = new URLSearchParams(url.search);
                        if (evt.target.value === 'rating') {
                          params.delete('sort');
                        } else {
                          params.set('sort', evt.target.value);
                        }
                        url.search = params.toString();
                        window.location.href = url.toString();
                      }"
                    >
                      <option value="rating" {{ $currentSort === 'rating' ? 'selected' : '' }}>Top Rated</option>
                      <option value="popular" {{ $currentSort === 'popular' ? 'selected' : '' }}>Most Popular</option>
                      <option value="newest" {{ $currentSort === 'newest' ? 'selected' : '' }}>Newest First</option>
                      <option value="oldest" {{ $currentSort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
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
                                <img src="{{ url($item->author->avatar) }}" alt="Avatar {{ $item->author->name }}">
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
                <p class="text-gray-500 text-sm mt-2">Total news: {{ $newsTotal }}</p>
              </div>
            </div>
        </div>
    </div>
  </section>
@push('js')
    <script src="{{ asset('assets/js/insights.js') }}"></script>
@endpush

@endsection
