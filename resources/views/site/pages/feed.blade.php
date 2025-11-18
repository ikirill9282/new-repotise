@extends('layouts.site')

@section('content')

  <div id="feed">
    @php
        if (isset($first_article) && $first_article) {
            array_unshift($articles, $first_article);
        }
        
        // Собираем все ID статей для исключения при поиске похожих
        $allArticleIds = collect($articles)->pluck('id')->toArray();
    @endphp

    @foreach ($articles as $key => $article)
        @if ($key == array_key_last($articles) - 1)
            <div class="stopper"></div>
        @endif
        @php
          // Исключаем текущую статью и все остальные уже загруженные
          $excludeIds = array_diff($allArticleIds, [$article->id]);
          $article->getAnalogs($excludeIds);
        @endphp
        @include('site.components.article_feed', [
          'article' => $article,
          'variables' => $page->config->keyBy('name'),
        ])
    @endforeach
    
    @if ($key == array_key_last($articles))
      <div class="stopper"></div>
    @endif

    @push('js')
        <script src="{{ asset('/assets/js/feed.js') }}"></script>
    @endpush
  </div>
@endsection