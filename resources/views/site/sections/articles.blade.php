@php
    $articles = $variables->firstWhere('name', 'article_ids')->value;
    $articles = \App\Models\Article::whereIn('id', $articles)->with('author')->get();
    while ($articles->count() < 3) {
        $articles = $articles->collect()->merge($articles)->slice(0, 3);
    }
@endphp

<section class="articles authorization_articles">
    <div class="container !mx-auto">
        <div class="about_block">
            @include('site.components.heading')
            <div class="block_cards !block">
            {{-- <div class="card-wrap "> --}}
                    <div class="swiper" id="swiper-articles">
                        <div class="swiper-wrapper">
                            @foreach ($articles as $article)
                                <div class="swiper-slide">
                                    <div class="item !w-full text-[#A4A0A0]">
                                        <a class="" href="{{ url($article->makeFeedUrl()) }}">
                                            <img src="{{ url($article->preview->image) }}"
                                                alt="Article {{ $article->id }}" />
                                        </a>
                                        <a class="mb-auto" href="{{ $article->makeFeedUrl() }}">
                                            <h3>{{ $article->title }}</h3>
                                        </a>
                                        <div class="print-content">{!! $article->short() !!}</div>
                                        <div class="name_author">
                                            <img class="rounded-full" src="{{ $article->author->avatar }}"
                                                alt="Avatar">
                                            <p>Автор {{ $article->author->getName() }}</p>
                                        </div>
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
