@php
$products = $variables->get('product_ids')->value;
$products = \App\Models\Product::query()
  ->whereIn('id', $products)
  ->with('preview', 'location', 'categories', 'type')
  ->withCount(['reviews' => function($query) {
    $query->whereNull('parent_id');
  }])
  ->get();

while($products->count() < 10) {
  $products = $products->collect()->merge($products)->slice(0, 10);
}
@endphp

<section class="popular_products">
  <div class="container !mx-auto">
      <div class="about_block">
          @include('site.components.heading')
          <div class="products_item !grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
              @foreach($products as $product)
                <div class="item h-full flex flex-col">
                    <div class="img_products">
                        <img src="{{ url($product->preview->image) }}" alt="Product {{ $product->id }} image" class="main_img">

                        @include('site.components.favorite.button', [
                          'stroke' => '#FF2C0C',
                          'type' => 'product',
                          'item_id' => $product->id,
                        ])

                        <a href="{{ url('/cart') }}" class="to_basket">
                          {{ print_var('cart_button_text', $variables) }}
                        </a>
                    </div>
                    <h3 class="text-nowrap overflow-hidden text-ellipsis">
                      <a class="transition !text-inherit hover:!text-black" href="{{ $product->makeUrl() }}">{{ $product->title }}</a>
                    </h3>
                    <div class="cost">
                        <p>${{ $product->price }}</p>
                        <span>${{ $product->old_price }}</span>
                    </div>
                    <div class="inf_cards flex flex-wrap">
                        <a class="text-nowrap" href="{{ url("/search?q={$product->type->title}") }}">{{ $product->type->title }}</a>
                        @foreach ($product->categories as $category)
                          <a class="text-nowrap" href="{{ url("/search?q={$category->title}") }}">{{ $category->title }}</a>
                        @endforeach
                        <a class="text-nowrap" href="{{ url("/search?q={$product->location->title}") }}">{{ $product->location->title }}</a>
                    </div>
                    <div class="stars_block !mt-auto">
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
          <a href="{{ print_var('more_link', $variables) }}" class="look_more">{{ print_var('more_text', $variables) }}</a>
      </div>
  </div>
</section>