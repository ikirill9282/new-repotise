@extends('layouts.site')

@section('content')
  <section class="home_tips relative">
    @include('site.components.parallax', ['class' => 'parallax-insights'])
    <div class="container relative z-20">
        <div class="about_block">
            @include('site.components.heading', ['variables' => $page->config->where(fn($item) => str_contains($item->name, 'page_')) ?? []])
            @include('site.components.breadcrumbs')
            <x-search placeholder="Search by keywords and tags..." />
        </div>
    </div>
  </section>
  <section class="tips_news_group articles-catalogue">
    <div class="container">
        <div class="about_block justify-betwee items-stretch !gap-12">
            <x-card size="sm" class="item_group basis-full">
                <x-title tag="h3" class="!font-normal !mb-6">Travel News</x-title>
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
                  <div class="travel-news-list grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @include('site.pages.insights.partials.news-items', ['items' => $newsItems])
                  </div>
                  <div class="travel-news-loader text-center py-4 text-[#FC7361] hidden">
                    Loading more news...
                  </div>
                </div>
            </x-card>
        </div>
    </div>
  </section>
@push('js')
    <script src="{{ asset('assets/js/insights.js') }}"></script>
@endpush

@endsection

