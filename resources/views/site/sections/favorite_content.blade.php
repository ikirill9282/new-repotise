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
                  <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                      @if (!empty(auth()->user()->favorite_products))
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
                                  @include('site.components.favorite.product', ['product' => $product])
                                @endforeach
                            </div>
                        </div>
                      @else
                        @include('site.components.favorite.empty')
                      @endif
                      
                      @include('site.components.recomend.wrapper')
                  </div>
                  <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                      @if (!empty(auth()->user()->favorite_authors))
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
                                  @include('site.components.favorite.author', ['author' => $author])
                                @endforeach
                            </div>
                        </div>
                      @else
                        @include('site.components.favorite.empty')
                      @endif
                      
                      @include('site.components.recomend.wrapper')
                  </div>
                </div>
            </div>
        </div>
    </div>
</section>