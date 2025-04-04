@php
    $article = $article->getFullComments()->getAnalogs()->getLikes();
@endphp

<div class="feed-item" data-content={{ $article->id }}>
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

                        <textarea class="outline-0 grow transition" rows="1" wrap="hard"
                            placeholder="{{ trim(print_var('comment_add_message', $variables)) }}" {{ auth()->check() ? '' : 'disabled' }}></textarea>

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
                    @if (!auth()->check())
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
</div>
