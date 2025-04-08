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
                        <div class="swiper-wrapper items-stretch">
                            @foreach ($articles as $article)
                                <div class="swiper-slide h-auto">
                                    <div class="item h-full flex flex-col items-stretch justify-start !w-full text-[#A4A0A0]">
                                        <a class="article-preview" href="{{ url($article->makeFeedUrl()) }}">
                                            <img src="{{ url($article->preview->image) }}"
                                                alt="Article {{ $article->id }}" />
                                        </a>
                                        <a class="mb-auto" href="{{ $article->makeFeedUrl() }}">
                                            <h3>{{ $article->title }}</h3>
                                        </a>
                                        <div class="print-content">{!! $article->short() !!}</div>
                                        <div class="name_author">
                                          <a class="group w-full flex items-center justify-start gap-2" href="{{ $article->author->makeProfileUrl() }}" class="author-link">
                                            <img class="rounded-full" src="{{ $article->author->avatar }}"
                                                alt="Avatar">
                                            <p class="">Author <span class="group-hover:!text-black transition">{{ $article->author->name }}</span></p>
                                          </a>
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
