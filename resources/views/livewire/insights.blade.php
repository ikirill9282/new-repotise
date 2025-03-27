<div>
    @foreach ($articles as $key => $article)
        @php
            $article = $article->getFullComments()->getAnalogs()->getLikes();
        @endphp
        <section class="name_articles">
            <section class="breadcrumb_block">
                <div class="container">
                    @include('site.components.breadcrumbs', [
                        'current_name' => $article['title'],
                        'exclude' => ['insights'],
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
        <section class="comments_group" @if (array_key_last($articles) == $key && !$end) id="stopper" @endif>
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
                                <a href="#" class="numbers">0/1000</a>
                                <a href="#" class="first_stick">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16" fill="none">
                                        <g clip-path="url(#clip0_1731_30973)">
                                            <path
                                                d="M11.6667 7.33333C11.6033 7.33333 11.5393 7.31533 11.482 7.27733L9.482 5.944C9.38933 5.882 9.33333 5.778 9.33333 5.66667C9.33333 5.55533 9.38867 5.45133 9.482 5.38933L11.482 4.056C11.6353 3.954 11.842 3.99533 11.944 4.14867C12.046 4.302 12.0047 4.50867 11.8513 4.61067L10.2673 5.66667L11.8513 6.72267C12.0047 6.82467 12.046 7.03133 11.944 7.18467C11.88 7.28133 11.7747 7.33333 11.6667 7.33333ZM4.51867 7.27733L6.51867 5.944C6.61133 5.882 6.66733 5.778 6.66733 5.66667C6.66733 5.55533 6.612 5.45133 6.51867 5.38933L4.51867 4.056C4.36467 3.954 4.158 3.99533 4.05667 4.14867C3.95467 4.302 3.996 4.50867 4.14933 4.61067L5.73333 5.66667L4.14933 6.72267C3.996 6.82467 3.95467 7.03133 4.05667 7.18467C4.12067 7.28133 4.22667 7.33333 4.33467 7.33333C4.398 7.33333 4.46133 7.31533 4.51867 7.27733ZM11.974 10.0167C12.0473 9.668 11.9667 9.31533 11.752 9.05067C11.5513 8.80333 11.2633 8.66733 10.94 8.66733H5.058C4.73467 8.66733 4.446 8.80333 4.24533 9.052C4.03067 9.31867 3.95133 9.67333 4.02733 10.024C4.376 11.628 5.88867 13.3333 8.00333 13.3333C10.118 13.3333 11.6327 11.6247 11.9727 10.0167H11.974ZM10.9407 9.334C11.092 9.334 11.184 9.408 11.2347 9.47067C11.3207 9.57733 11.3533 9.73 11.322 9.87867C11.036 11.23 9.77667 12.6667 8.00467 12.6667C6.23267 12.6667 4.97333 11.232 4.68 9.882C4.64733 9.73133 4.68 9.57733 4.76533 9.47067C4.816 9.408 4.90733 9.33333 5.05867 9.33333H10.9407V9.334ZM16.0007 8.00067C16 3.58867 12.4113 0 8 0C3.58867 0 0 3.58867 0 8C0 12.4113 3.58867 16 8 16C12.4113 16 16 12.4113 16 8L16.0007 8.00067ZM15.334 8.00067C15.334 12.044 12.044 15.334 8.00067 15.334C3.95733 15.334 0.666667 12.0433 0.666667 8C0.666667 3.95667 3.95667 0.666667 8 0.666667C12.0433 0.666667 15.3333 3.95667 15.3333 8L15.334 8.00067Z"
                                                fill="#A4A0A0" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_1731_30973">
                                                <rect width="16" height="16" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                                <a href="#" class="third_stick">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16" fill="none">
                                        <path d="M13.166 7.81706L3.16602 7.81706" stroke="#A4A0A0" stroke-width="0.7"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M9.13281 3.80083L13.1661 7.81683L9.13281 11.8335" stroke="#A4A0A0"
                                            stroke-width="0.7" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="block_commends">
                        @foreach ($article->comments as $comment)
                            @include('site.components.comment', ['comment' => $comment])
                        @endforeach

                        <div class="more_commends_group">
                          <a href="#">Load More Comments (50 of 248)</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="similar_materials">
        <div class="container">
            <div class="about_block">
                <h2>You Might Also Like</h2>
                <div class="materials_group">
                    <div class="swiper" id="analogs-swiper-{{ $article->id }}">
                        <div class="swiper-wrapper">
                          @if (isset($article->analogs) && !empty($article->analogs))
                            @foreach ($article->analogs as $analog)
                              <div class="swiper-slide">
                                  <div class="cards_group">
                                      <a href="{{ $analog->makeInsightsUrl() }}">
                                        <img src="{{ url($analog->preview->image) }}" alt="Analog {{ $analog->id }}" class="main_img">
                                      </a>
                                      <a href="{{ $analog->makeInsightsUrl() }}">
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
