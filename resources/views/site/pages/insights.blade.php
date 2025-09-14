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
  <section class="tips_news_group">
    <div class="container">
        <div class="about_block justify-betwee items-stretch !gap-12">
            <x-card class="item_group basis-9/12 lg:basis-4/5">
                <x-title tag="h3" class="!font-normal !mb-10">Travel Insights</x-title>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                  @foreach($articles as $item)
                    <div class="cards_group">
                        <a href="{{ $item->makeFeedUrl() }}">
                          <img src="{{ $item->preview->image }}" alt="Article {{ $item->id }}" class="main_img">
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

                @include('site.components.paginator', ['paginator' => $articles])
            </x-card>
            <div class="basis-3/12 lg:basis-1/5">
              <x-title class="!font-normal md:!text-[1.7rem] !mb-6">Travel News</x-title>
              <x-last-news></x-last-news>
            </div>
        </div>
    </div>
  </section>
@endsection