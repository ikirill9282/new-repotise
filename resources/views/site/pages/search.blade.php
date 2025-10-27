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
                  <x-search placeholder="Search for travel guides, maps, itineraries...">
                    @if (isset($tags) && !empty($tags))
                        <div div class="name_tags flex-wrap">
                            @foreach ($tags as $tag)
                                <a class="text-nowrap" href="{{ url("search?q=". urlencode($tag['title']) ."") }}">{{ $tag['title'] }}</a>
                            @endforeach
                        </div>
                    @endif
                  </x-search>
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
                        <select class="custom-select">
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
																							<a href="{{ url("/products/{$item['slug']}?pid=" . \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $item['id']])) }}">
                                                <img src="{{ url(print_key('preview', $item)) }}" alt=""
                                                    class="img_profile rounded-full">
																							</a>
                                            </div>
                                            <div class="right_text">
                                                <h3>
                                                  <a  
                                                    class="!text-inherit"
                                                    href="{{ url("/products/{$item['slug']}?pid=" . \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $item['id']])) }}"
                                                    >
                                                      {!! print_key('title', $item) !!}
                                                  </a>
                                                </h3>
                                                <div class="profile">
                                                    <img class="rounded-full object-cover" src="{{ print_key('avatar', $item['author']) }}"
                                                        alt="Avatar {{ print_key('profile', $item['author']) }}">
                                                    <a href="{{ url('/profile/' . print_key('profile', $item['author'] ?? [])) }}"
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
                                                    @php
                                                      try {
                                                        $a1 = $item['calcedPrice'];
                                                        $a2 = $item['priceWithoutDiscount'];
                                                      } catch (\Exception $e) {
                                                        dd($item);
                                                      }
                                                    @endphp
                                                    <p>{{ currency($item['calcedPrice']) }}</p>
                                                    <span>{{ currency($item['priceWithoutDiscount']) }}</span>
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
																							<a href="{{ url('/users/profile/' . print_key('profile', $item)) }}">
                                                <img src="{{ url(print_key('avatar', $item)) }}" alt="Profile"
                                                    class="img_profile rounded-full object-cover">
																							</a>
                                            </div>

                                            <div class="right_text">
                                                <h3 class=""><a class="!text-inherit"
                                                        href="{{ url('/users/profile/' . print_key('profile', $item)) }}">{{ print_key('name', $item) }}</a>
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
																							<a href="{{ url('/insights/feed?aid=' . print_key('id', $item)) }}">
                                                <img src="{{ url(print_key('preview', $item)) }}" alt="Insight"
                                                    class="img_profile rounded-full">
																							</a>
                                            </div>
                                            <div class="right_text">
                                                <h3><a class="!text-inherit"
                                                        href="{{ url('/insights/feed?aid=' . print_key('id', $item)) }}">{{ print_key('title', $item) }}</a>
                                                </h3>
                                                <div class="print-content hover:cursor-pointer hover-text-black transition">
                                                    {!! print_key('short', $item) !!}
                                                </div>
                                                <div class="profile">
                                                    <img class="rounded-full object-cover"
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
                            {{-- <div class="col-lg-4 col-md-6">
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
                            </div> --}}
														<div class="col-lg-4 col-md-6">
                                <div class="item">
                                    <h4>
																				Discover Trending Articles
																		</h4>
                                    <a href="/insights">Travel Insights</a>
                                </div>
                            </div>
														<div class="col-lg-4 col-md-6">
                                <div class="item">
                                    <h4>
																				Explore Travel Products
																		</h4>
                                    <a href="/products">Product Catalog</a>
                                </div>
                            </div>
														<div class="col-lg-4 col-md-6">
                                <div class="item">
                                    <h4>
																				Meet Our Travel Experts
																		</h4>
                                    <a href="/creators">Creators</a>
                                </div>
                            </div>
                            {{-- <div class="col-lg-4 col-md-6">
                                <div class="item">
                                    @include('site.components.heading', ['variables' => $variables->filter(fn($var) => str_contains($var->name, 'box3'))])
                                    <a
                                        href="{{ print_var('box3_link', $variables) }}">{{ print_var('box3_button_label', $variables) }}</a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
