@php
  // dd($variables);
    $articles = $variables->firstWhere('name', 'insights_article_ids')->value;
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
                                    @include('site.components.cards.aricle', [
                                        'model' => $article,
                                        'template' => 'main',
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                {{-- </div> --}}
            </div>
            <a href="{{ print_var('insights_more_link', $variables) }}" class="look_more">
                {{ print_var('insights_more_text', $variables) }}
            </a>
        </div>
    </div>
</section>
