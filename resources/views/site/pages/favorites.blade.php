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
                                <span>Sort by: </span>
                                <div style="min-width: 150px;">
                                    <x-form.select 
                                        name="favorites-products-sort"
                                        :compact="true"
                                        :label="$productsSort === 'price_high' ? 'Price: High to Low' : ($productsSort === 'price_low' ? 'Price: Low to High' : ($productsSort === 'rating' ? 'Top Rated' : ($productsSort === 'popular' ? 'Most Popular' : ($productsSort === 'newest' ? 'Newest First' : 'Oldest First'))))"
                                        :options="[
                                            'price_high' => 'Price: High to Low',
                                            'price_low' => 'Price: Low to High',
                                            'rating' => 'Top Rated',
                                            'popular' => 'Most Popular',
                                            'newest' => 'Newest First',
                                            'oldest' => 'Oldest First',
                                        ]"
                                        :tooltip="false"
                                        labelClass="!text-black"
                                        value="{{ $productsSort }}"
                                    />
                                </div>
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
                                <span>Sort by: </span>
                                <div style="min-width: 150px;">
                                    <x-form.select 
                                        name="favorites-creators-sort"
                                        :compact="true"
                                        :label="$creatorsSort === 'name_asc' ? 'Name (A-Z)' : ($creatorsSort === 'name_desc' ? 'Name (Z-A)' : 'Followers (High to Low)')"
                                        :options="[
                                            'name_asc' => 'Name (A-Z)',
                                            'name_desc' => 'Name (Z-A)',
                                            'followers_desc' => 'Followers (High to Low)',
                                        ]"
                                        :tooltip="false"
                                        labelClass="!text-black"
                                        value="{{ $creatorsSort }}"
                                    />
                                </div>
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
            const handleSortChange = (inputName, param, defaultValue) => {
                const input = document.querySelector(`input[name="${inputName}"]`);
                if (!input) {
                    return;
                }

                // Устанавливаем начальное значение
                const currentValue = input.value || defaultValue;
                input.value = currentValue;
                
                // Сохраняем предыдущее значение для проверки изменений
                let previousValue = currentValue;
                let isInitialized = false;

                // Обработчик изменения значения
                let timeoutId;
                input.addEventListener('input', function() {
                    // Пропускаем первое событие при инициализации
                    if (!isInitialized) {
                        isInitialized = true;
                        return;
                    }
                    
                    const value = this.value;
                    
                    // Проверяем, изменилось ли значение
                    if (value === previousValue) {
                        return;
                    }
                    
                    previousValue = value;
                    
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        const url = new URL(window.location.href);
                        const params = url.searchParams;
                        
                        // Проверяем, нужно ли менять URL
                        const currentUrlValue = params.get(param) || defaultValue;
                        if (value === currentUrlValue) {
                            return; // URL уже соответствует значению
                        }

                        if (value === defaultValue) {
                            params.delete(param);
                        } else {
                            params.set(param, value);
                        }

                        url.search = params.toString();
                        window.location.href = url.toString();
                    }, 100);
                });
                
                // Помечаем как инициализированное после небольшой задержки
                setTimeout(() => {
                    isInitialized = true;
                }, 200);
            };

            handleSortChange('favorites-products-sort', 'products_sort', 'rating');
            handleSortChange('favorites-creators-sort', 'creators_sort', 'name_asc');
        });
    </script>
@endpush