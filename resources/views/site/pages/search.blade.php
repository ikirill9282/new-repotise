@extends('layouts.site')


@php
 $variables = $page->variables;
//  dd($variables);   
@endphp
@section('content')
    <section class="search_result_home relative">
        @include('site.components.parallax', ['class' => 'parallax-search'])
        <div class="container !mx-auto relative z-50">
            <div class="about_block">
                @include('site.components.heading', ['variables' => $variables->filter(fn($var) => str_contains($var->name, 'page'))])
                @include('site.components.breadcrumbs')
                <div class="input_group flex-wrap !justify-start">
                    @include('site.components.search', [
                        'placeholder' => print_var('search_placeholder', $variables ?? null),
                    ])

                    @if (isset($tags) && !empty($tags))
                        <div div class="name_tags">
                            @foreach ($tags as $tag)
                                <a href="{{ url("search?q=". urlencode($tag['title']) ."") }}">{{ $tag['title'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if (isset($search_results) && is_array($search_results) && !empty($search_results))
        <section class="results_seach">
            <div class="container !mx-auto">
                <div class="about_block">
                    <div class="right_select">
                        <span>Sort by:</span>
                        <select>
                            <option>Relevance</option>
                            <option>Relevance1</option>
                            <option>Relevance2</option>
                        </select>
                    </div>
                    <div class="result_cards">
                        @foreach ($search_results as $item)
                            @switch($item['index'])
                                @case('products')
                                    <div class="card_group">
                                        <div class="left_title">
                                            <img src="{{ asset('assets/img/basket_search.svg') }}" alt="Product icon">
                                            <p>Product</p>
                                        </div>
                                        <div class="profile_commendor">
                                            <div class="img-wrap">
                                                <img src="{{ url(print_key('preview', $item)) }}" alt=""
                                                    class="img_profile rounded-full">
                                            </div>
                                            <div class="right_text">
                                                <h3>
                                                  @php
                                                    $path = match ($item['index']) {
                                                      'products' => '/products/' . $item['location']['slug'] . '/' . print_key('slug', $item) . '?pid=' . \App\Helpers\CustomEncrypt::generateUrlHash(['id' => print_key('id', $item)]),
                                                      //  => ,
                                                    };
                                                  @endphp
                                                  <a  
                                                    class="!text-inherit"
                                                    href="{{ url($path) }}"
                                                    >
                                                      {!! print_key('title', $item) !!}
                                                  </a>
                                                </h3>
                                                <div class="profile">
                                                    <img class="rounded-full" src="{{ print_key('avatar', $item['author']) }}"
                                                        alt="Avatar {{ print_key('profile', $item['author']) }}">
                                                    <a href="{{ url('/users/profile/' . print_key('profile', $item['author'] ?? [])) }}"
                                                        class="!text-[#A4A0A0] hover:cursor-pointer hover:!text-black transition">
                                                        {{ print_key('profile', $item['author']) }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="right_group_for_mob">
                                            <div class="left_title mob">
                                                <img src="{{ asset('assets/img/basket_search.svg') }}" alt="Product icon">
                                                <p>Product</p>
                                            </div>
                                            <div class="right_reviews_group">
                                                <div class="cost">
                                                    <p>${{ print_key('price', $item) }}</p>
                                                    <span>${{ print_key('old_price', $item) }}</span>
                                                </div>
                                                <div class="reviews">
                                                    <div class="stars flex">
                                                        {{-- TODO: render stars --}}
                                                        @foreach (rating_images(print_key('rating', $item) ?? 0) as $star)
                                                            <span><img src="{{ $star }}" alt=""></span>
                                                        @endforeach
                                                    </div>
                                                    <h5 class="text-nowrap">{{ print_key('reviews_count', $item) }} Reviews</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @break

                                @case('users')
                                    <div class="card_group">
                                        <div class="left_title">
                                            <img src="{{ asset('assets/img/profile_search.svg') }}" alt="Profile icon">
                                            <p>Creator</p>
                                        </div>
                                        <div class="profile_commendor">
                                            <div class="img-wrap">
                                                <img src="{{ url(print_key('avatar', $item)) }}" alt="Profile"
                                                    class="img_profile rounded-full">
                                            </div>
                                            <div class="right_text">
                                                <h3 class=""><a class="!text-inherit"
                                                        href="{{ url('/users/profile/' . print_key('profile', $item)) }}">{{ print_key('profile', $item) }}</a>
                                                </h3>
                                                <div>
                                                    {!! print_key('description', $item) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="right_group_for_mob">
                                            <div class="left_title mob">
                                                <img class="" src="{{ asset('assets/img/profile_search.svg') }}"
                                                    alt="Profile icon">
                                                <p>Creator</p>
                                            </div>
                                            <div class="right_reviews_group">
                                                <div class="reviews">
                                                    <h5 class="text-nowrap">
                                                        <img src="{{ asset('assets/img/followers.svg') }}" alt="Followers">
                                                        {{ print_key('followers_count', $item) }} Followers
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @break

                                @case('articles')
                                    <div class="card_group">
                                        <div class="left_title">
                                            <img src="{{ asset('assets/img/document_search.svg') }}" alt="Insight icon">
                                            <p>Travel <br>
                                                Insight</p>
                                        </div>
                                        <div class="profile_commendor">
                                            <div class="img-wrap">
                                                <img src="{{ url(print_key('preview', $item)) }}" alt="Insight"
                                                    class="img_profile rounded-full">
                                            </div>
                                            <div class="right_text">
                                                <h3><a class="!text-inherit"
                                                        href="{{ url('/insights/feed?aid=' . print_key('id', $item)) }}">{{ print_key('title', $item) }}</a>
                                                </h3>
                                                <div class="print-content hover:cursor-pointer hover-text-black transition">
                                                    {!! print_key('short', $item) !!}
                                                </div>
                                                <div class="profile">
                                                    <img class="rounded-full"
                                                        src="{{ url(print_key('avatar', $item['author'] ?? [])) }}" alt="Avatar">
                                                    <a href="{{ url('/users/profile/' . print_key('profile', $item['author'] ?? [])) }}"
                                                        class="!text-[#A4A0A0] hover:cursor-pointer hover:!text-black transition">{{ print_key('profile', $item['author'] ?? []) }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="right_group_for_mob">
                                            <div class="left_title mob">
                                                <img src="{{ asset('assets/img/document_search.svg') }}" alt="Insight icon">
                                                <p>Travel <br>
                                                    Insight</p>
                                            </div>
                                            <div class="right_reviews_group">
                                                <span
                                                    class="date">{{ \Illuminate\Support\Carbon::parse(print_key('created_at', $item))->format('d.m.Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @break
                            @endswitch
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="caribbean_islands">
            <div class="container">
                <div class="about_block">
                    @include('site.components.heading', [
                        'variables' => $variables->filter(fn($var) => str_contains($var->name, 'notfound')),
                        'append' => '"<span>' . request()->get('q') . '</span>"',
                    ])
                    <p>{{ print_var('subtitle', $variables) }}</p>
                    <div class="block_cards">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="item">
                                    @include('site.components.heading', ['variables' => $variables->filter(fn($var) => str_contains($var->name, 'box1'))])
                                    <a
                                        href="{{ print_var('box1_link', $variables) }}">{{ print_var('box1_button_label', $variables) }}</a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="item">
                                    @include('site.components.heading', ['variables' => $variables->filter(fn($var) => str_contains($var->name, 'box2'))])
                                    <a
                                        href="{{ print_var('box2_link', $variables) }}">{{ print_var('box2_button_label', $variables) }}</a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="item">
                                    @include('site.components.heading', ['variables' => $variables->filter(fn($var) => str_contains($var->name, 'box3'))])
                                    <a
                                        href="{{ print_var('box3_link', $variables) }}">{{ print_var('box3_button_label', $variables) }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
