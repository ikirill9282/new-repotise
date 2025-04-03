<div id="feed">
    @php
        $articles = $articles->items();
        if ($first_article) {
            array_unshift($articles, $first_article);
        }
    @endphp
    @foreach ($articles as $key => $article)
        @php
            $article = $article->getFullComments()->getAnalogs()->getLikes();
        @endphp

        @if ($perPage == 1)
            <div x-intersect="$wire.loadNextArticle"></div>
        @elseif ($key == $perPage - 3)
            <div x-intersect="$wire.loadNextArticle"></div>
        @endif
        <div class="feed-item">

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
                        @include('site.components.last_news', ['news' => $last_news])
                    </div>
                </div>
            </section>
            <section class="comments_group">
                <div class="container">
                    <div class="about_block">
                        <div class="title_block">
                            @include('site.components.heading', ['title' => 'comment'])
                            <span>{{ $article->countComments() }}</span>
                        </div>
                        <div class="write_comment_group">
                            <div class="write_comment !items-start">
                                <h3> {{ auth()->check() ? auth()->user()->profile : '' }}</h3>
                                
                                <textarea
                                  class="outline-0 grow transition"
                                  rows="1"
                                  wrap="hard"
                                  placeholder="{{ trim(print_var('comment_add_message', $variables)) }}"
                                  {{ auth()->check() ? '' : 'disabled' }}></textarea>
                                
                                <div class="right_stickers">
                                  <a href="#" class="numbers {{ auth()->check() ? '' : 'disabled' }}">0/1000</a>
                                  <a href="#" class="first_stick {{ auth()->check() ? '' : 'disabled' }}">
                                      @include('icons.smiles')
                                  </a>
                                  <a href="#" class="third_stick {{ auth()->check() ? '' : 'disabled' }}">
                                      @include('icons.arrow_right')
                                  </a>
                                </div>
                            </div>
                            @if(!auth()->check())
                              <a href="#" class="go_comment open_auth">Login to comment</a>
                            @endif
                        </div>
                        <div class="block_commends">
                            @foreach ($article->comments as $comment)
                                @include('site.components.comment', ['comment' => $comment])
                            @endforeach

                            <div class="more_commends_group">
                                <a href="#">{{ print_var('comment_more_comments', $variables) }} (50 of 248)</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="similar_materials">
                <div class="container">
                    <div class="about_block">
                        @include('site.components.heading', ['title' => 'analog'])
                        <div class="materials_group">
                            <div class="swiper" id="analogs-swiper-{{ $article->id }}">
                                <div class="swiper-wrapper">
                                    @if (isset($article->analogs) && !empty($article->analogs))
                                        @foreach ($article->analogs as $analog)
                                            <div class="swiper-slide">
                                                <div class="cards_group">
                                                    <a href="{{ $analog->makeFeedUrl() }}">
                                                        <img src="{{ url($analog->preview->image) }}"
                                                            alt="Analog {{ $analog->id }}" class="main_img">
                                                    </a>
                                                    <a href="{{ $analog->makeFeedUrl() }}">
                                                        <h3>{{ $analog->title }}</h3>
                                                    </a>
                                                    <p>{!! $analog->short() !!}</p>
                                                    <div class="date">
                                                        <span>{{ \Illuminate\Support\Carbon::parse($analog->created_at)->format('d.m.Y') }}</span>
                                                        <div class="name_author">
                                                            <img src="{{ url($analog->author->avatar) }}"
                                                                alt="Avatar">
                                                            <p>{{ $analog->author->profile }}</p>
                                                        </div>
                                                    </div>
                                                </div>
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
    @endforeach

    @push('js')
        <script>
            $.fn.isInViewport = function() {
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                history.scrollRestoration = 'manual';
                // scrollTo({top: 0, behavior: 'instant'});
            });
        </script>
        <script>
            window.addEventListener('refresh-page', event => {
                window.location.reload(false);
                //  scrollTo({top: 0, behavior: 'instant'});
            })
        </script>
    @endpush
    @script
        <script>
            const init_sliders = () => {
              const items = document.querySelectorAll('div[id*="analogs-swiper-"]');
              
              const sliders = [...items].map((elem) => {
                  const selector = elem.getAttribute('id');
                  return new Swiper(`#${selector}`, {
                      slidesPerView: 4,
                      spaceBetween: 20,
                      navigation: {
                          nextEl: `#${selector} .swiper-button-next`,
                          prevEl: `#${selector} .swiper-button-prev`,
                      },
                      breakpoints: {
                          320: {
                              slidesPerView: 1.1,
                              spaceBetween: 10,
                          },
                          400: {
                              slidesPerView: 1.3,
                              spaceBetween: 10,
                          },
                          500: {
                              slidesPerView: 1.6,
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
              });
              
              return sliders;
            }

            let sli = init_sliders();
            let writers = new CommentWriters();
            let editors = new Editors();
            
            Livewire.hook('morphed', ({ el, component }) => {
                sli = init_sliders();
                writers = new CommentWriters();
                editors = new Editors();
            });

        </script>
    @endscript
</div>
