@extends('layouts.site')

@php
  $variables = $page->variables;
  $hasProducts = auth()->user()->favorite_products->isNotEmpty();
  $hasAuthors = auth()->user()->favorite_authors->isNotEmpty(); 
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
                          <div class="top_group_fav favorites_second {{ !$hasAuthors ? '!hidden' : '' }}">
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

                        <x-empty 
                          :class="$hasAuthors ? 'hidden' : ''" 
                          title="You're not following any authors yet. Discover creators and follow your favorites!"
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
@endpush