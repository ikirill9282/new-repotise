@extends('layouts.site')

@php
  $variables = $page->variables;
  $favoriteProducts = $favoriteProducts ?? collect();
  $favoriteAuthors = $favoriteAuthors ?? collect();
  $favoriteArticles = $favoriteArticles ?? collect();
  $hasProducts = $favoriteProducts->isNotEmpty();
  $hasAuthors = $favoriteAuthors->isNotEmpty();
  $hasArticles = $favoriteArticles->isNotEmpty();
  $productsSort = $productsSort ?? request()->get('products_sort', 'rating');
  $creatorsSort = $creatorsSort ?? request()->get('creators_sort', 'name_asc');
@endphp

@section('content')
  <section class="favorites_home relative">
    @include('site.components.parallax', ['class' => 'parallax-favorite'])
    <div class="container relative z-10">
        <div class="about_block">
            @include('site.components.heading', ['variables' => $variables->filter(fn($item) => str_contains($item->name, 'page'))])
            @include('site.components.breadcrumbs')
        </div>
    </div>
  </section>
  <section class="products_favorite products_second_block">
    <div class="container">
        <div class="about_block">
            <div class="tab_menu">
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-primary fw-semibold position-relative {{ (!$hasProducts && $hasAuthors) ? '' : 'active' }}" id="pills-home-tab"
                            data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab"
                            aria-controls="pills-home" aria-selected="true">Saved Products</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link second_link text-primary fw-semibold position-relative {{ (!$hasProducts && $hasAuthors) ? 'active' : '' }}"
                            id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button"
                            role="tab" aria-controls="pills-profile" aria-selected="false">Followed
                            Creators</button>
                    </li>
                </ul>
            </div>
            <div class="sections_menu">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade {{ (!$hasProducts && $hasAuthors) ? '' : 'show active' }}" id="pills-home" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        <div class="favorites-content">
                          <div class="top_group_fav favorites_second">
                            <div class="right_select {{ !$hasProducts ? '!hidden' : '' }}">
                                <span>Sort by:</span>
                                <select id="favorites-products-sort" class="tg-select">
                                    <option value="price_high" {{ $productsSort === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="price_low" {{ $productsSort === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="rating" {{ $productsSort === 'rating' ? 'selected' : '' }}>Top Rated</option>
                                    <option value="popular" {{ $productsSort === 'popular' ? 'selected' : '' }}>Most Popular</option>
                                    <option value="newest" {{ $productsSort === 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ $productsSort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                </select>
                            </div>
                            <div class="favorite_cards_group">
                                @foreach ($favoriteProducts as $product)
                                    @include('site.components.cards.product', [
                                        'model' => $product,
                                        'class' => 'removable',
                                    ])
                                @endforeach
                            </div>
                        </div>
                        <x-empty 
                          :class="$hasProducts ? 'hidden' : ''" 
                          title="Your Favorites list is currently emply. Start adding products you love!"
                        />
                        </div>

                        @include('site.components.recomend.wrapper', [
                            'models' => auth()->user()->getRecomendProducts(),
                            'card' => 'product',
                        ])
                    </div>
                    <div class="tab-pane fade {{ (!$hasProducts && $hasAuthors) ? 'show active' : '' }}" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <div class="favorites-content">
                          <div class="top_group_fav favorites_second {{ !$hasArticles ? '!hidden' : '' }}">
                            <div class="right_select">
                                <span>Sort by:</span>
                                <select id="favorites-creators-sort" class="tg-select">
                                    <option value="name_asc" {{ $creatorsSort === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                    <option value="name_desc" {{ $creatorsSort === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                    <option value="followers_desc" {{ $creatorsSort === 'followers_desc' ? 'selected' : '' }}>Followers (High to Low)</option>
                                </select>
                            </div>
                            <div class="cards_why_need">
                                @foreach ($favoriteArticles as $article)
                                    @include('site.components.favorite.article', [
                                        'article' => $article,
                                        'class' => 'removable',
                                    ])
                                @endforeach
                            </div>
                        </div>

                        <x-empty 
                          :class="$hasArticles ? 'hidden' : ''" 
                          title="No articles from followed creators yet. Follow creators to see their articles here!"
                        />
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
@endsection

@push('js')
    <script src="{{ asset('/assets/js/favorite.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const handleSortChange = (selectId, param, defaultValue) => {
                const select = document.getElementById(selectId);
                if (!select) {
                    return;
                }

                select.addEventListener('change', (event) => {
                    const url = new URL(window.location.href);
                    const params = url.searchParams;
                    const value = event.target.value;

                    if (value === defaultValue) {
                        params.delete(param);
                    } else {
                        params.set(param, value);
                    }

                    url.search = params.toString();
                    window.location.href = url.toString();
                });
            };

            handleSortChange('favorites-products-sort', 'products_sort', 'rating');
            handleSortChange('favorites-creators-sort', 'creators_sort', 'name_asc');
        });
    </script>
@endpush