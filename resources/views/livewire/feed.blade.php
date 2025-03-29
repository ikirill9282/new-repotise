<div>
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
        @elseif ($key == ($perPage - 2))
            <div x-intersect="$wire.loadNextArticle"></div>
        @endif

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
        {{-- <section class="comments_group" @if (array_key_last($articles) == $key && !$end) id="stopper" @endif> --}}
        <section class="comments_group">
            <div class="container">
                <div class="about_block">
                    <div class="title_block">
                        @include('site.components.heading', ['title' => 'comment'])
                        <span>{{ $article->countComments() }}</span>
                    </div>
                    <div class="write_comment_group">
                        <div class="write_comment">
                            <h3>@talmaev1</h3>
                            <input type="text" placeholder="{{ print_var('comment_add_message', $variables) }}">
                            <div class="right_stickers">
                                <a href="#" class="numbers">0/1000</a>
                                <a href="#" class="first_stick">
                                    @include('icons.smiles')
                                </a>
                                <a href="#" class="third_stick">
                                    @include('icons.arrow_right')
                                </a>
                            </div>
                        </div>
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
                    @include('site.components.heading', ['title' => 'subscribe'])
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
                                                        <img src="{{ url($analog->author->avatar) }}" alt="Avatar">
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
            Livewire.hook('morphed', ({
                el,
                component
            }) => {
                console.log('morphed');
                sli = init_sliders();
            })
        </script>
    @endscript
</div>
