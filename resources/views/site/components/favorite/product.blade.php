
<div class="item">
  <div class="img_products">
      @include('site.components.favorite.button', [
        'stroke' => '#FF2C0C',
        'type' => 'product',
        'item_id' => $product->id,
      ])
      <img src="{{ $product->preview->image }}" alt="Product Image" class="main_img">
      <a href="#" class="to_basket">Add to cart</a>
  </div>
  {{-- <h3>{{ $product->title }}</h3> --}}
  <h3><a class="transition !text-inherit hover:!text-black" href="{{ $product->makeUrl() }}">{{ $product->title }}</a></h3>
  <div class="cost">
      <a>${{ $product->price }}</a>
      <span>${{ $product->old_price }}</span>
  </div>
  <div class="inf_cards flex-wrap">
      <a class="text-nowrap" href="{{ url("/search?q={$product->type->title}") }}">{{ $product->type->title }}</a>
      <a class="text-nowrap" href="{{ url("/search?q={$product->type->title}") }}">{{ $product->location->title }}</a>
      @foreach ($product->categories as $category)
        <a class="text-nowrap" href="{{ url("/search?q={$category->title}") }}">{{ $category->title }}</a>
      @endforeach
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