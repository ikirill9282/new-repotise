@php
    $article = $article->getFullComments()->getAnalogs()->getLikes();
    // dd($article->comments);
@endphp

<div class="feed-item" data-content={{ $article->id }}>
    <section class="name_articles">
        <section class="breadcrumb_block">
            <div class="container">
                @include('site.components.breadcrumbs', [
                    'current_name' => $article['title'],
                    'exclude' => ['feed'],
                ])
            </div>
        </section>
        <div class="container">
            <div class="about_block">
                @include('site.components.article_view', ['article' => $article])
                @include('site.components.last_news')
            </div>
        </div>
    </section>
    <section class="comments_group">
        <div class="container">
            {{-- @dd($article) --}}
            @include('site.components.comments.wrap', ['model' => $article, 'type' => 'article'])
        </div>
    </section>
    <section class="similar_materials">
        <div class="container">
            <div class="about_block">
                @include('site.components.heading', [
                  'variables' => $variables->filter(fn($item) => str_contains($item->name, 'analog')),
                ])
                <div class="materials_group">
                    <div class="swiper" id="analogs-swiper-{{ $article->id }}">
                        <div class="swiper-wrapper">
                            @if (isset($article->analogs) && !empty($article->analogs))
                                @foreach ($article->analogs as $analog)
                                    <div class="swiper-slide">
                                        @include('site.components.cards.aricle', [
                                            'model' => $analog,
                                        ])
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="swiper-button-next">
                            @include('icons.analog_arrow_prev')
                        </div>
                        <div class="swiper-button-prev">
                            @include('icons.analog_arrow_next')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
