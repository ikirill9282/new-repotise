@php
    $trending_products = \App\Models\Product::getTrendingProducts();
    $getQueryString = fn(array $query) => http_build_query(array_merge(request()->query(), $query))
@endphp
<section class="filter_products">
    <div class="container">
        <div class="about_block">
            <div class="trending_cards">
                @include('site.components.heading', ['title' => 'trending'])
                <div class="products_item">
                    @if ($trending_products->isNotEmpty())
                        <div class="swiper mySwiper">
                            <div class="swiper-wrapper">
                                @foreach ($trending_products as $product)
                                    <div class="swiper-slide">
                                        @include('site.components.cards.product', ['model' => $product])
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-next"><svg xmlns="http://www.w3.org/2000/svg" width="40"
                                    height="40" viewBox="0 0 40 40" fill="none">
                                    <g opacity="0.6">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M20.4173 4.5835C11.904 4.5835 5.00065 11.4852 5.00065 20.0002C5.00065 28.5135 11.904 35.4168 20.4173 35.4168C28.9306 35.4168 35.834 28.5135 35.834 20.0002C35.834 11.4852 28.9307 4.5835 20.4173 4.5835Z"
                                            fill="#212121" stroke="#212121" stroke-width="1.5"
                                            stroke-linecap="square" />
                                        <path d="M17 14L22.81 19.785L17 25.57" stroke="white" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </g>
                                </svg></div>
                            <div class="swiper-button-prev"><svg xmlns="http://www.w3.org/2000/svg" width="40"
                                    height="40" viewBox="0 0 40 40" fill="none">
                                    <g opacity="0.6">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M20.4173 4.5835C11.904 4.5835 5.00065 11.4852 5.00065 20.0002C5.00065 28.5135 11.904 35.4168 20.4173 35.4168C28.9306 35.4168 35.834 28.5135 35.834 20.0002C35.834 11.4852 28.9307 4.5835 20.4173 4.5835Z"
                                            fill="#212121" stroke="#212121" stroke-width="1.5"
                                            stroke-linecap="square" />
                                        <path d="M17 14L22.81 19.785L17 25.57" stroke="white" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </g>
                                </svg></div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="search_filter">
                @include('site.components.search', [
                    'template' => 'products',
                    'placeholder' => print_var('search_placeholder', $variables ?? null),
										'attributes' => [
											'data-source' => 'products',
										],
                ])
                <div class="search_results">
                    @foreach (\App\Models\Category::limit(10)->get() as $category)
                        <span>
                          <a class="px-2" href="{{ url("/products?" . $getQueryString(['categories' => $category->slug])) }}">
                            {{ $category->title }}
                          </a>
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="filter_products_group">
                <div class="filter">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    {{ print_var('filter_title', $variables) }}
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="calculate_filtr">
                                        <div class="accordion accordion-flush" id="accordionFlushExample">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                                        {{ print_var('filter_rating', $variables) }}
                                                    </button>
                                                </h2>
                                                <div id="flush-collapseOne" class="accordion-collapse collapse"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
                                                        <div class="stars_filter">
                                                            <span class="numbers">0</span>
                                                            <div class="stars">
                                                                <span data-value="1">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="30" height="30"
                                                                        viewBox="0 0 16 17" fill="none">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                                                            stroke="#FFDB0C" stroke-width="0.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                    </svg>
                                                                </span>
                                                                <span data-value="2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="30" height="30"
                                                                        viewBox="0 0 16 17" fill="none">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                                                            stroke="#FFDB0C" stroke-width="0.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                    </svg>
                                                                </span>
                                                                <span data-value="3">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="30" height="30"
                                                                        viewBox="0 0 16 17" fill="none">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                                                            stroke="#FFDB0C" stroke-width="0.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                    </svg>
                                                                </span>
                                                                <span data-value="4">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="30" height="30"
                                                                        viewBox="0 0 16 17" fill="none">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                                                            stroke="#FFDB0C" stroke-width="0.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                    </svg>
                                                                </span>
                                                                <span data-value="5">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="30" height="30"
                                                                        viewBox="0 0 16 17" fill="none">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M8.73617 2.81852L9.95445 5.25236C10.0738 5.49129 10.3043 5.65697 10.5716 5.69531L13.2969 6.0876C13.9702 6.18481 14.2382 7.00088 13.7509 7.46848L11.7801 9.36215C11.5864 9.54837 11.4983 9.81605 11.5441 10.0789L12.0092 12.7524C12.1237 13.4137 11.4198 13.9183 10.818 13.6054L8.38214 12.3423C8.14335 12.2184 7.85735 12.2184 7.61786 12.3423L5.182 13.6054C4.58015 13.9183 3.87626 13.4137 3.9915 12.7524L4.4559 10.0789C4.50171 9.81605 4.41355 9.54837 4.21988 9.36215L2.24912 7.46848C1.76181 7.00088 2.02976 6.18481 2.70311 6.0876L5.42843 5.69531C5.69569 5.65697 5.92685 5.49129 6.04625 5.25236L7.26383 2.81852C7.5651 2.21674 8.4349 2.21674 8.73617 2.81852Z"
                                                                            stroke="#FFDB0C" stroke-width="0.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <span class="numbers">5</span>
                                                        </div>
                                                        <div class="price">
                                                            <p>Price</p>
                                                            <div class="slider-container">
                                                                <div class="block_input">
                                                                    <span></span>
                                                                    <input id="range-slider-7" class="slider"
                                                                        type="range" min="0" max="9999999"
                                                                        step="1000" value="5000000"
                                                                        oninput="updateMaxPrice(this.value, 7)">
                                                                </div>
                                                                <div class="price-range">
                                                                    <span id="min-price-7">$0</span>
                                                                    <span id="max-price-7">$9,999,999</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo"
                                                        aria-expanded="false" aria-controls="flush-collapseTwo">
                                                        {{ print_var('filter_type', $variables) }}
                                                    </button>
                                                </h2>
                                                <div id="flush-collapseTwo" class="accordion-collapse collapse"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
                                                        <div class="type_products">
                                                            @foreach (\App\Models\Type::all() as $type)
                                                                <a 
                                                                  href="{{ url('/products?' . $getQueryString(['type' => $type->slug])) }}"
                                                                  class="{{ request()->has('type') && request()->get('type') == $type->slug ? 'active' : '' }}"
                                                                  >
                                                                    {{ $type->title }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#flush-collapseThree" aria-expanded="false"
                                                        aria-controls="flush-collapseThree">
                                                        {{ print_var('filter_category', $variables) }}
                                                    </button>
                                                </h2>
                                                <div id="flush-collapseThree" class="accordion-collapse collapse"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
																											@include('site.components.search', [
																													'icon' => false,
																													'placeholder' => print_var(
																															'search_filter_category_placeholder',
																															$variables ?? null),
																													'template' => 'filters',
																													'hits' => 'filter-category',
																													'attributes' => [
																														'data-source' => 'categories',
																													]
																											])
																											<div class="input-group">
																												<div class="search_block">
																													<div class="search_results categories-results">
																														</div>
																												</div>
																											</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse1"
                                                        aria-expanded="false" aria-controls="flush-collapse1">
																												{{ print_var('filter_location', $variables) }}
                                                    </button>
                                                </h2>
                                                <div id="flush-collapse1" class="accordion-collapse collapse"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
																											@include('site.components.search', [
																												'icon' => false,
																												'placeholder' => print_var(
																														'search_filter_category_placeholder',
																														$variables ?? null
                                                        ),
																												'template' => 'filters',
																												'hits' => 'filter-location',
																												'attributes' => [
																													'data-source' => 'locations'
																												]
																											])
																											<div class="input-group">
																												<div class="search_block">
																													<div class="search_results locations-results">
																														</div>
																												</div>
																											</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="on_sale">
                                                <label class="custom-checkbox">
                                                    <input type="checkbox">
                                                    <span class="checkmark"></span>
                                                    <span class="text">{{ print_var('filter_sale', $variables) }}</span>
                                                </label>
                                            </div>
                                            <div class="buttons" id="filter_button">
                                                <button>{{ print_var('filter_button', $variables) }}</button>
                                                <a href="#">{{ print_var('filter_clear', $variables) }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_products_filter">
                    <div class="right_select">
                        <span>Sort by:</span>
                        <select>
                            <option>Top Rated</option>
                            <option>Top Rated1</option>
                            <option>Top Rated2</option>
                        </select>
                    </div>
                    <div class="filter_cards_group">
                        @foreach ($paginator->all() as $item)
                            @include('site.components.cards.product', ['model' => $item])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('js')
  <script>
    $(document).ready(function() {
      $('.accordion').find('.search-input').on('searchItemSelected', function(evt, data) {
        const block = $(this).closest('.accordion-body');
        const result = block.find('.search_results');
        
        const item = $('<span>');
        const remove = $('<a>', {
          href: '#',
          class: 'disabled',
        });

        remove.on('click', function(evt) {
          evt.preventDefault();
          $(this).parents('span').detach();
        })

        remove.html('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none"><path d="M3 3.5C5.34315 5.84315 6.65686 7.15685 9 9.5M3 9.5C5.34315 7.15685 6.65686 5.84315 9 3.5" stroke="#A4A0A0" stroke-width="0.5" stroke-linecap="round" /> </svg>')

        item.attr('data-value', data.slug);
        item.text(data.label);
        item.append(remove);
        if (!result.find('span[data-value="'+ data.slug +'"]').length) {
          result.append(item);
        }
      });
      
      $('.filter_button').on('click', function(evt) {
        evt.preventDefault();
        const filter_wrap = $(this).closest('.filter');
        const stars_wrap = filter_wrap.find('.stars');
        const types_wrap = filter_wrap.find('.type_products');
        const categories_wrap = filter_wrap.find('.categories-result');
        const locations_wrap = filter_wrap.find('.locations-results');
        const price = filter_wrap.find('#range-slider-7');

        const data = {};

        
        
      });

    });
  </script>
@endpush
