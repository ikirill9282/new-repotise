@php
$article = \App\Models\Article::find($variables->firstWhere('name', 'article_id')->value);
@endphp

<section class="why_need_baby_monitor">
  <div class="container">
      <div class="about_block">
          <div class="left_text">
              @include('site.components.heading', [
                'variables' => $variables, 
                'title' => $article->title
              ])
              <p>{!! $article->short(600) !!}</p>
              <div class="name_author">
                  <img src="{{ url($article->author->avatar) }}" alt="Article {{ $article->id }}">
                  <p>Author {{ $article->author->getName() }}</p>
              </div>
          </div>
          <img src="{{ url($article->preview->image) }}" alt="Article {{ $article->id }}" alt="" class="img_main">
      </div>
  </div>
</section>