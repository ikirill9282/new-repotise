@php
$articles = $variables->firstWhere('name', 'article_ids')->value;
$articles = \App\Models\Article::whereIn('id', $articles)->with('author')->get();
while ($articles->count() < 3) {
    $articles = $articles->collect()->merge($articles)->slice(0, 6);
}
@endphp

<section class="articles authorization_articles">
  <div class="container">
      <div class="about_block">
          @include('site.components.heading', ['variables' => $variables])
          <div class="row">
              @foreach($articles as $article)
              <div class="col-lg-4 col-md-6">
                  <div class="item">
                      <a href="{{ $article->makeInsightsUrl() }}">
                        <img src="{{ url($article->preview->image) }}" alt="Article Preview" class="img_main">
                      </a>
                      <a href="{{ $article->makeInsightsUrl() }}">
                        <h3>{{ $article->title }}</h3>
                      </a>
                      <p>{!! $article->short() !!}</p>
                      <div class="name_author">
                          <img src="{{ asset('/assets/img/img_author.png') }}" alt="Avatar">
                          <p>Автор {{ $article->author->getName() }}</p>
                      </div>
                  </div>
              </div>
              @endforeach
              <a href="{{ $variables->get('more_link')->value }}" class="look_more">{{ $variables->get('more_text')->value }}</a>
          </div>
      </div>
  </div>
</section>