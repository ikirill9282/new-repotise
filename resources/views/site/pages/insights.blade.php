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
        <div class="about_block">
            <div class="item_group">
                <h3>Travel Insights</h3>
                {{-- <div class="row"> --}}
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
                        {{-- <div class="col-lg-4 col-md-6">
                        </div> --}}
                      @endforeach
                    </div>
                {{-- </div> --}}
                @include('site.components.paginator', ['paginator' => $articles])
            </div>
            @include('site.components.last_news', ['variables' => $page->config])
        </div>
    </div>
  </section>
@endsection