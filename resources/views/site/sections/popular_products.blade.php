@php
$products = $variables->get('product_ids')->value;
$products = \App\Models\Product::query()
  ->whereIn('id', $products)
  ->with('preview', 'location', 'categories', 'type')
  ->withCount(['reviews' => function($query) {
    $query->whereNull('parent_id');
  }])
  ->get();

$products = collect(array_fill(0, 10, $products->first()));
@endphp

<section class="popular_products">
  <div class="container">
      <div class="about_block">
          @include('site.components.heading', ['variables' => $variables])
          @foreach ($products->chunk(5) as $key => $products_chunk)
            <div class="products_item @if($key == 1) last_products @endif">
                @foreach($products_chunk as $product)
                  <div class="item">
                      <div class="img_products">
                          <img src="{{ url($product->preview->image) }}" alt="Product Preview" class="main_img">
                          <a href="{{ url('/user/favourite/add') }}" class="span_buy">
                            <svg xmlns="http://www.w3.org/2000/svg" width="27"
                                  height="26" viewBox="0 0 27 26" fill="none">
                                  <path
                                      d="M24.0064 10.4387C23.9957 7.69156 22.5544 5.10802 19.7128 4.19262C17.7617 3.56297 15.6363 3.91313 13.9857 6.28289C12.3351 3.91313 10.2097 3.56297 8.25852 4.19262C5.41667 5.10812 3.97536 7.69215 3.96494 10.4397C3.93865 15.9037 9.47535 20.0849 13.9843 22.0833L13.9857 22.0827L13.9871 22.0833C18.4962 20.0848 24.0334 15.9032 24.0064 10.4387Z"
                                      stroke="#FF2C0C" stroke-width="1.5" stroke-linecap="square" />
                              </svg>
                            </a>
                          <a href="{{ url('/user/cart/add') }}" class="to_basket">
                            {{ $variables->get('cart_button_text')->value }}
                          </a>
                      </div>
                      <h3>{{ $product->title }}</h3>
                      <div class="cost">
                          <p>${{ $product->price }}</p>
                          <span>${{ $product->old_price }}</span>
                      </div>
                      <div class="product-additional">
                        <div class="product-additional__item producuct-additional__type">{{ $product->type->title }}</div>
                        <div class="product-additional__item product-additional__category">
                          @if (isset($product->categories) && $product->categories->isNotEmpty())
                            {{ $product->categories->first()->title }}
                          @endif
                        </div>
                        <div class="product-additional__item product-additional__locaion">{{ $product->location->title }}</div>
                      </div>
                      <div class="stars_block">
                          <div class="stars">
                              @foreach ($product->prepareRatingImages() as $image)
                                <span><img src="{{ $image }}" alt="Star"></span>
                              @endforeach
                          </div>
                          <div class="commends">
                              <span>{{ $product->reviews_count }} Reviews</span>
                          </div>
                      </div>
                  </div>
                @endforeach
            </div>
          @endforeach
          <a href="{{ $variables->get('more_link')->value }}" class="look_more">
            {{ $variables->get('more_text')->value }}
          </a>
      </div>
  </div>
</section>