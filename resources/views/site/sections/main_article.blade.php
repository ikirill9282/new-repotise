@php
$article = \App\Models\Article::find($variables->firstWhere('name', 'article_id')->value);
@endphp

<section class="why_need_baby_monitor">
  <div class="container !mx-auto">
      <div class="about_block !items-stretch">
          <div class="left_text">
              @include('site.components.heading', [
                'variables' => $variables, 
                'header_text' => $article->title
              ])
              <div class="print-content text-[#A4A0A0]">{!! $article->short(600) !!}</div>
              <div class="name_author">
                <a class="group w-full flex items-center justify-start gap-2" href="{{ $article->author->makeProfileUrl() }}" class="author-image">
                  <img src="{{ url($article->author->avatar) }}" alt="Article {{ $article->id }}">
                  <p>Author <span class="group-hover:!text-black transition">{{ $article->author->name }}</span></p>
                </a>
              </div>
          </div>
          <div class="ma-main-img">
            <img src="{{ url($article->preview->image) }}" alt="Article {{ $article->id }}" alt="" class="img_main">
          </div>
        </div>
  </div>
</section>