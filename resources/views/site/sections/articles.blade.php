@php
    $articles = $variables->firstWhere('name', 'article_ids')->value;
    $articles = \App\Models\Article::whereIn('id', $articles)->with('author')->get();
    while ($articles->count() < 3) {
        $articles = $articles->collect()->merge($articles)->slice(0, 3);
    }
@endphp

<section class="articles authorization_articles">
    <div class="container">
        <div class="about_block">
            @include('site.components.heading', ['variables' => $variables])
            <div class="block_cards">
                @foreach ($articles as $article)
                    <div class="item">
                        <a href="{{ url($article->makeFeedUrl()) }}">
                          <img src="{{ url($article->preview->image) }}" alt="Article {{ $article->id }}" class="img_main">
                        </a>
                        <a href="{{ $article->makeFeedUrl() }}">
                            <h3>{{ $article->title }}</h3>
                        </a>
                        <p>{!! $article->short() !!}</p>
                        <div class="name_author">
                            <img src="{{ $article->author->avatar }}" alt="Avatar">
                            <p>Автор {{ $article->author->getName() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ $variables->get('more_link')->value }}" class="look_more">
              {{ $variables->get('more_text')->value }}
            </a>
        </div>
    </div>
</section>
