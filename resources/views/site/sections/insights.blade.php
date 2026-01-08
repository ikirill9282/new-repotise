@php
  // dd($variables);
    $articleIds = [];
    if ($variables && $variables instanceof \Illuminate\Support\Collection) {
      $articleItem = $variables->firstWhere('name', 'insights_article_ids');
      $articleIds = $articleItem?->value ?? [];
      if (!is_array($articleIds)) {
        $articleIds = [];
      }
    }
    $articles = !empty($articleIds) ? \App\Models\Article::whereIn('id', $articleIds)->with('author')->get() : collect();
    
    // Безопасное дублирование элементов, если их меньше 3
    if ($articles->count() > 0 && $articles->count() < 3) {
      $originalCount = $articles->count();
      $needed = 3 - $originalCount;
      $duplicates = $articles->take($needed);
      $articles = $articles->merge($duplicates);
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
                                      <a class="!h-48 xs:!h-64 !w-full rounded-lg overflow-hidden" href="{{ url($article->makeFeedUrl()) }}">
                                          <img class="!object-cover !w-full !h-full" src="{{ url($article->preview->image) }}"
                                              alt="model {{ $article->id }}" />
                                      </a>
                                      <a class="mb-auto" href="{{ $article->makeFeedUrl() }}">
                                          <h3>{{ $article->title }}</h3>
                                      </a>
                                      <div class="print-content">{{ strip_tags($article->short()) }}</div>
                                      <div class="name_author">
                                        <a class="group w-full flex items-center justify-start gap-2" href="{{ $article->author->makeProfileUrl() }}" class="author-link">
                                          <img class="rounded-full object-cover" src="{{ $article->author->avatar }}"
                                              alt="Avatar">
                                          <p class=""><span class="group-hover:!text-black transition">{{ $article->author->name }}</span></p>
                                        </a>
                                      </div>
                                    </div>
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
