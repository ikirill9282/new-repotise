@php
  $hash = \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $model->id]);
  $cart = new \App\Services\Cart();
@endphp

@if(isset($template) && $template == 'cart')
  <div class="item">
    <img src="{{ url($model->preview->image) }}" alt="Product Preview"
        class="order_img">
    <div class="description_orders">
        <div class="title_description">
            <h4><a
                    href="{{ $model->makeUrl() }}">{{ $model->title }}</a>
            </h4>
            <h5>
              <span>{{ currency($model->getPrice()) }}</span>
              <span>{{ currency($model->getPriceWithoutDiscount()) }}</span>
            </h5>
        </div>
        <p>{{ $model->categories->pluck('title')->join(', ') }}</p>
        <div class="w-full flex justify-between items-center">
          <div class="counter"
            data-item="{{ $hash }}">
            <button class="btn minus">−</button>
            <span class="count">{{ $model->pivot->count ?? $model->pivot['count'] }}</span>
            <button class="btn plus">+</button>
        </div>
        <div class="drop cart-drop hover:cursor-pointer"  data-item="{{ $hash }}" data-key="{{ \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $model->id]) }}">
          <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24" fill="transparent">
            <rect width="24" height="24" fill="white"/>
            <path d="M5 7.5H19L18 21H6L5 7.5Z" stroke="currentColor" stroke-linejoin="round"/>
            <path d="M15.5 9.5L15 19" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 9.5V19" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M8.5 9.5L9 19" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M16 5H19C20.1046 5 21 5.89543 21 7V7.5H3V7C3 5.89543 3.89543 5 5 5H8M16 5L15 3H9L8 5M16 5H8" stroke="currentColor" stroke-linejoin="round"/>
            <script xmlns=""/></svg>
        </div>
        </div>
    </div>
  </div>
@else
  <div class="item flex flex-col {{ isset($class) ? $class : '' }}">
    <div class="img_products relative">
        <img class="main_img object-cover" src="{{ url($model->preview->image) }}" alt="model {{ $model->id }} image">

        @include('site.components.favorite.button', [
          'stroke' => '#FF2C0C',
          'type' => 'product',
          'item_id' => $model->id,
        ])

        @if ($model->subscription)
          <x-btn href="{{ $model->makeUrl() }}" class="to_basket absolute bottom-0 !left-[50%] translate-x-[-50%] !w-[90%] !py-2.5">Subscribe</x-btn>
        @else
          <a 
            href="{{ url('/cart') }}" 
            class="to_basket !left-[50%] translate-x-[-50%] add-to-cart {{ $cart->inCart($model->id) ? 'in-cart' : '' }}" 
            data-value="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $model->id]) }}"
            data-key="{{ \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $model->id]) }}"
          >
            {{ $cart->inCart($model?->id) ? 'In cart' : print_var('cart_button_text', $variables ?? []) ?? 'Add to cart' }}
          </a>
        @endif
    </div>
    <h3 class="text-nowrap overflow-hidden text-ellipsis">
      <a class="transition !text-inherit hover:!text-black" href="{{ $model->makeUrl() }}">{{ $model->title }}</a>
    </h3>
    <div class="cost">
      <p>{{ currency($model->getPrice()) }}</p>
      <span>{{ currency($model->getPriceWithoutDiscount()) }}</span>
    </div>
    <div class="inf_cards flex flex-wrap">
        {{-- TYPES --}}
        @foreach ($model->types->shuffle()->slice(0, 3) as $type)
          <a class="text-nowrap" href="{{ url("/products/?type={$type->slug}") }}">{{ $type->title }}</a>
        @endforeach

        {{-- CATEGORIES --}}
        @foreach ($model->categories->shuffle()->slice(0, 3) as $category)
          <a class="text-nowrap" href="{{ url("/search?q={$category->title}") }}">{{ $category->title }}</a>
        @endforeach

        {{-- LOCATIONS --}}
        @foreach ($model->locations->shuffle()->slice(0, 3) as $location)
          <a class="text-nowrap" href="{{ url("/products/{$location->slug}") }}">{{ $location->title }}</a>
        @endforeach
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
@endif