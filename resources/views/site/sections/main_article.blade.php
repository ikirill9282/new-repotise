@php
// dd($variables);
$mainArticleId = $variables ? ($variables->firstWhere('name', 'main_article_id')?->value ?? null) : null;
$article = $mainArticleId ? \App\Models\Article::find($mainArticleId) : null;
$mainImageUrl = $article && $article->preview ? url($article->preview->image) : null;
@endphp

@if($article)
  @if($mainImageUrl)
    @push('head')
      <link rel="preload" as="image" href="{{ $mainImageUrl }}" fetchpriority="high">
    @endpush
  @endif

  <section class="why_need_baby_monitor">
    <div class="container !mx-auto">
        <div class="about_block !items-stretch">
          <div class="left_text">
            @include('site.components.heading', [
              'variables' => $variables, 
              'header_text' => $article->title,
            ])
            <div class="print-content text-[#A4A0A0]">{{ strip_tags($article->short(800)) }}</div>
            <div class="name_author">
              <a class="group w-full flex items-center justify-start gap-2" href="{{ $article->author->makeProfileUrl() }}" class="author-image">
                <img 
                    class="objcet-cover" 
                    src="{{ url($article->author->avatar) }}" 
                    alt="Article {{ $article->id }}"
                    loading="lazy"
                />
                <p> <span class="group-hover:!text-black transition">{{ $article->author->name }}</span></p>
              </a>
            </div>
          </div>
          <div class="ma-main-img">
            @if($mainImageUrl)
            <a href="{{ $article->makeFeedUrl() }}">
              <img 
                  src="{{ $mainImageUrl }}" 
                  alt="Article {{ $article->id }}" 
                  class="img_main"
                  loading="eager"
                  fetchpriority="high"
                  decoding="async"
              />
            </a>
            @endif
          </div>
        </div>
    </div>
  </section>
@endif