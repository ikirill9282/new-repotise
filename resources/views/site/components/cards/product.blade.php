<div class="item flex flex-col {{ isset($class) ? $class : '' }}">
  <div class="img_products relative">
      <img class="main_img object-cover" src="{{ url($model->preview->image) }}" alt="model {{ $model->id }} image">

      @include('site.components.favorite.button', [
        'stroke' => '#FF2C0C',
        'type' => 'product',
        'item_id' => $model->id,
      ])

      <a href="{{ url('/cart') }}" class="to_basket !left-[50%] translate-x-[-50%]">
        {{ print_var('cart_button_text', $variables) ?? 'Add to cart' }}
      </a>
  </div>
  <h3 class="text-nowrap overflow-hidden text-ellipsis">
    <a class="transition !text-inherit hover:!text-black" href="{{ $model->makeUrl() }}">{{ $model->title }}</a>
  </h3>
  <div class="cost">
      <p>${{ $model->price }}</p>
      <span>${{ $model->old_price }}</span>
  </div>
  <div class="inf_cards flex flex-wrap">
      {{-- @dd($model->type->title) --}}
      <a class="text-nowrap" href="{{ url("/search?q={$model->type->title}") }}">{{ $model->type->title }}</a>
      @foreach ($model->categories as $category)
        <a class="text-nowrap" href="{{ url("/search?q={$category->title}") }}">{{ $category->title }}</a>
      @endforeach
      <a class="text-nowrap" href="{{ url("/search?q={$model->location->title}") }}">{{ $model->location->title }}</a>
  </div>
  <div class="stars_block !mt-auto">
      <div class="stars">
        @foreach ($model->prepareRatingImages() as $image)
          <span><img src="{{ $image }}" alt="Star"></span>
        @endforeach
      </div>
      <div class="commends">
          <span>{{ $model->reviews_count }} Reviews</span>
      </div>
  </div>
</div>