<section class="products_favorite products_second_block">
    <div class="container">
        <div class="about_block">
            <div class="tab_menu">
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-primary fw-semibold active position-relative" id="pills-home-tab"
                            data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab"
                            aria-controls="pills-home" aria-selected="true">Saved Products</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link second_link text-primary fw-semibold position-relative"
                            id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button"
                            role="tab" aria-controls="pills-profile" aria-selected="false">Followed
                            Creators</button>
                    </li>
                </ul>
            </div>
            <div class="sections_menu">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        <div class="favorites-content">
                          <div class="top_group_fav favorites_second">
                            <div class="right_select">
                                <span>Sort by:</span>
                                <select>
                                    <option>Top Rated</option>
                                    <option>Top Rated1</option>
                                    <option>Top Rated2</option>
                                </select>
                            </div>
                            <div class="favorite_cards_group">
                                @foreach (auth()->user()->favorite_products as $product)
                                    @include('site.components.cards.product', [
                                        'model' => $product,
                                        'class' => 'removable',
                                    ])
                                @endforeach
                            </div>
                        </div>
                        @include('site.components.favorite.empty', [
                            'class' => auth()->user()->favorite_products->isEmpty() ? '' : 'hidden',
                        ])
                        </div>

                        @include('site.components.recomend.wrapper', [
                            'models' => auth()->user()->getRecomendProducts(),
                            'card' => 'product',
                        ])
                    </div>
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <div class="favorites-content">
                          <div class="top_group_fav favorites_second">
                            <div class="right_select">
                                <span>Sort by:</span>
                                <select>
                                    <option>Newest First</option>
                                    <option>Newest First1</option>
                                    <option>Newest First2</option>
                                </select>
                            </div>
                            <div class="cards_why_need">
                                @foreach (auth()->user()->favorite_authors as $author)
                                    @include('site.components.favorite.author', [
                                        'author' => $author,
                                        'class' => 'removable',
                                    ])
                                @endforeach
                            </div>
                        </div>

                        @include('site.components.favorite.empty', [
                            'class' => auth()->user()->favorite_authors->isEmpty() ? '' : 'hidden',
                        ])
                        </div>

                        @include('site.components.recomend.wrapper', [
                            'models' => auth()->user()->getRecomendAuthors(),
                            'card' => 'author',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('js')
    <script>
        const favoriteCallback = () => {

        }
        $(window).on('favoriteUpdated', function(evt, data) {
            const elem = $(data.element);
            const block = elem.parents('.item');
            const parent = block.parents('.tab-pane');
            const wrap = parent.find('.favorites_second');
            const empty = parent.find('.empty-block');
            const analogs = $('.favorite-button[data-key="' + elem.data('key') + '"]')


            const toggleEmpty = (callback = null) => {
                const items = wrap.find('.item');

                if (!items.length) {
                    empty.fadeIn();
                    empty.removeClass('hidden')
                } else {
                    empty.hasClass('hidden') ? (callback === null ? true : callback()) : empty.fadeOut(() => {
                        empty.addClass('hidden')
                        callback()
                    });
                }
            }

            const hideElement = (obj, byHeight = false) => {
                if (byHeight) {
                    obj.animate({ opacity: 0 });
                    obj.animate({ height: 'toggle' }, 400, 'swing', () => obj.detach() && toggleEmpty());
                } else {
                    obj.animate({ opacity: 0 }, 400, 'swing', () => obj.css({ height: '0px' }));
                    obj.animate({ width: 'toggle', 'flex-basis': '0%', }, 400, 'swing', () => obj.detach() && toggleEmpty());
                }
            }

            if (!data.result.value) {
                if (block.hasClass('removable')) {
                    hideElement(block, (data.result.type === 'author'));
                }

                if (analogs.length) {
                    analogs.each((key, item) => {
                        const analog = $(item).parents('.item');
                        if (analog.hasClass('removable')) {
                            hideElement(analog);
                        }
                    })
                }
            } else {
                if (data.result.type === 'author') {
                    $.ajax({
                        method: 'POST',
                        url: '/api/data/favorite-author',
                        data: {
                            _token: getCSRF(),
                            id: data.result.model_id,
                        }
                    }).then(response => {
                        if (response.status) {
                            const new_block = $(response.content);
                            new_block.addClass('removable');
                            new_block.addClass('hidden');
                            FavoriteButtons().discover(new_block);
                            parent.find('.cards_why_need').prepend(new_block)
                            toggleEmpty(() => {
                                new_block.fadeOut(() => new_block.removeClass('hidden'));
                            });
                        }
                    })
                }

                if (data.result.type === 'product') {
                    const clone = block.clone();
                    clone.addClass('removable');
                    clone.addClass('hidden');
                    FavoriteButtons().discover(clone);
                    wrap.find('.favorite_cards_group').prepend(clone)
                    toggleEmpty(() => {
                        clone.fadeOut(() => clone.removeClass('hidden'));
                    });
                }
            }
        });
    </script>
@endpush
