<div>
    @foreach ($articles as $key => $article)
      @php
        $article = $article->getFullComments()->getAnalogs()->getLikes();
        // dd($article);
      @endphp
        <section class="article">
            <section class="name_articles">
                @include('site.components.breadcrumbs', ['current_name' => $article['title'], 'exclude' => ['insights']])
                <div class="container">
                    <div class="about_block">
                        @include('site.components.article_view', ['article' => $article])
                        @include('site.components.last_news', ['news' => $last_news])
                    </div>
                </div>
            </section>
              <sectopn class="comments_group" @if(array_key_last($articles->all()) == $key && !$end) id="stopper" @endif>
                <div class="container">
                    <div class="about_block">
                        <div class="title_block">
                            <h2>Comments</h2>
                            <span>{{ $article->countComments() }}</span>
                        </div>
                        <div class="write_comment_group">
                            <div class="write_comment">
                                <h3>@talmaev1</h3>
                                <input type="text" placeholder="Add a comment...">
                                <div class="right_stickers">
                                    <a wire:click.prevent="" href="#" class="first_stick">
                                        @include('icons.smiles')
                                    </a>
                                    <a wire:click.prevent="" href="#" class="second_stick">
                                        @include('icons.upfile')
                                    </a>
                                    <a wire:click.prevent="" href="#" class="third_stick">
                                        @include('icons.arrow_right')
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="block_commends">
                            @foreach ($article->comments as $comment)
                              @include('site.components.comment', ['comment' => $comment])
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="similar_materials">
                <div class="container">
                    <div class="about_block">
                        <h2>You Might Also Like</h2>
                        <div class="materials_group">
                            <div class="swiper" id="analogs-swiper-{{ $key }}">
                                <div class="swiper-wrapper">
                                    @if (isset($article->analogs) && !empty($article->analogs))
                                      @foreach ($article->analogs as $analog)
                                        <div class="swiper-slide">
                                            <div class="cards_group">
                                                <a href="{{ $analog->makeInsightsUrl() }}"><img src="{{ $analog->preview->image }}" alt="Preview"
                                                        class="main_img"></a>
                                                <a href="{{ $analog->makeInsightsUrl() }}">
                                                    <h3>{{ $analog->title }}</h3>
                                                </a>
                                                <p class="!font-sm">{!! $analog->short() !!}</p>
                                                <div class="date">
                                                    <span>{{ \Illuminate\Support\Carbon::parse($analog->created_at)->format('d.m.Y') }}</span>
                                                    <div class="name_author">
                                                        <img src="{{ $analog->author->avatar }}" alt="Avatar">
                                                        <p>Автор {{ $analog->author->getName() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                      @endforeach
                                    @endif
                                </div>
                                <div class="swiper-button-next">
                                  @include('icons.analog_arrow_next')
                                </div>
                                <div class="swiper-button-prev">
                                  @include('icons.analog_arrow_prev')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
    @endforeach

    @push('js')
          <script>
            $.fn.isInViewport = function () {
                let elementTop = $(this).offset().top;
                let elementBottom = elementTop + $(this).outerHeight();

                let viewportTop = $(window).scrollTop();
                let viewportBottom = viewportTop + $(window).height();

                return elementBottom > viewportTop && elementTop < viewportBottom;
            };
            $(window).scroll((event) => {
              $('#stopper').each(function(i, el) {
                  if ($(this).isInViewport()) {
                      $(this).addClass('test');
                      $(this).removeAttr('id');
                      Livewire.dispatch('load-next-article');
                  } 
              })
            });
          </script>
    @endpush
    @script 
      <script>
        const init_sliders = () => {
          const items = document.querySelectorAll('div[id*="analogs-swiper-"]');
          const sliders = [...items].forEach((elem) => {
            const selector = elem.getAttribute('id');
            new Swiper(`#${selector}`, {
              slidesPerView: 4,
                spaceBetween: 20,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                breakpoints: {
                    320: {
                        slidesPerView: 1.2,
                        spaceBetween: 10,
                    },
                    400: {
                        slidesPerView: 1.4,
                        spaceBetween: 10,
                    },
                    500: {
                        slidesPerView: 1.7,
                        spaceBetween: 10,
                    },
                    600: {
                        slidesPerView: 1.9,
                        spaceBetween: 10,
                    },
                    700: {
                        slidesPerView: 2.2,
                        spaceBetween: 10,
                    },
                    768: {
                        slidesPerView: 2.2,
                        spaceBetween: 15,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    1200: {
                        slidesPerView: 4,
                        spaceBetween: 20,
                    },
                },
            });
          })

          return sliders;
        }
        let sli = init_sliders();
        Livewire.hook('morphed',  ({ el, component }) => {
          console.log('morphed');
          sli = init_sliders();
        })
      </script>
    @endscript 
</div>
