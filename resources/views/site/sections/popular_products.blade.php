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
          <h2>Trending Products</h2>
          <div class="products_item">
              @foreach($products as $product)
                <div class="item">
                    <div class="img_products">
                        <img src="{{ url($product->preview->image) }}" alt="Product {{ $product->id }} image" class="main_img">
                        <a href="{{ url('/user/favorite/add/product') }}" class="span_buy">
                          @include('icons.favorite', ['stroke' => '#FF2C0C'])
                        </a>
                        <a href="{{ url('/user/cart/add') }}" class="to_basket">
                          {{ $variables->get('cart_button_text')?->value ?? '' }}
                        </a>
                    </div>
                    <h3>{{ $product->title }}</h3>
                    <div class="cost">
                        <p>${{ $product->price }}</p>
                        <span>${{ $product->old_price }}</span>
                    </div>
                    <div class="inf_cards">
                        <a href="#">{{ $product->type->title }}</a>
                        <a href="#">{{ $product->categories->first()->title }}</a>
                        <a href="#">{{ $product->location->title }}</a>
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
          <a href="{{ $variables->get('more_link')?->value ?? '#' }}" class="look_more">{{ $variables->get('more_text')?->value ?? '' }}</a>
      </div>
  </div>
</section>