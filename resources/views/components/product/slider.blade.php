@props([
  'id' => null,
  'products' => [],
])

@php
  $cart = new \App\Services\Cart();
@endphp

<div 
  x-data="{ swiper: null }" x-init="swiper = new Swiper($refs.container, {
      loop: true,
      slidesPerView: 1.3,
      autoHeight: true,
      spaceBetween: 10,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      breakpoints: {
        420: { slidesPerView: 1.5 },
        420: { slidesPerView: 2.2 },
        576: { slidesPerView: 2.6 },
        600: { slidesPerView: 3 },
        768: { slidesPerView: 2 },
        900: { slidesPerView: 3 },
        1024: { slidesPerView: 2 },
        1200: { slidesPerView: 3 },
      },
    })"
  class=""
>
  <div class="swiper" x-ref="container" id="{{ $id }}">
    <div class="swiper-wrapper !items-stretch">
      @foreach ($products as $product)
        <div class="swiper-slide">
          <div class="h-full flex flex-col gap-2">
            <div class="relative h-60 rounded overflow-hidden">
                <img 
                  class="max-w-full h-full object-cover" 
                  src="{{ url($product->preview->image) }}" 
                  alt="product {{ $product->id }} image"
                >

                @include('site.components.favorite.button', [
                  'stroke' => '#FF2C0C',
                  'type' => 'product',
                  'item_id' => $product->id,
                  'class' => 'absolute top-2 right-2 bg-white p-1.5 rounded-lg',
                  'width' => 20,
                  'height' => 20,
                ])

                <x-btn
                  href="{{ url('/cart') }}" 
                  class="absolute !w-9/10 bottom-0 left-[50%] translate-x-[-50%] !rounded add-to-cart {{ $cart->inCart($product->id) ? 'in-cart' : '' }}" 
                  data-value="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $product->id]) }}"
                  data-key="{{ \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $product->id]) }}"
                >
                  {{ $cart->inCart($product?->id) ? 'In cart' : print_var('cart_button_text', $variables ?? []) ?? 'Add to cart' }}
                </x-btn>
            </div>
            <div class="text-gray text-nowrap overflow-hidden text-ellipsis">
              <a 
                class="transition !text-inherit hover:!text-active" 
                href="{{ $product->makeUrl() }}"
              >
                {{ $product->title }}
              </a>
            </div>
            <div class="flex justify-start items-center gap-2">
                <div class="">${{ number_format($product->price) }}</div>
                <div class="text-gray line-through">${{ number_format($product->old_price) }}</div>
            </div>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($product->types->shuffle()->slice(0, 3) as $type)
                  <a class="text-xs !text-gray !bg-light text-nowrap px-2 py-1 rounded !transition hover:!bg-second hover:!text-light" href="{{ url("/products/?type={$type->slug}") }}">{{ $type->title }}</a>
                @endforeach

                @foreach ($product->categories->shuffle()->slice(0, 3) as $category)
                  <a class="text-xs !text-gray !bg-light text-nowrap px-2 py-1 rounded !transition hover:!bg-second hover:!text-light" href="{{ url("/search?q={$category->title}") }}">{{ $category->title }}</a>
                @endforeach
                
                @foreach ($product->locations->shuffle()->slice(0, 3) as $location)
                  <a class="text-xs !text-gray !bg-light text-nowrap px-2 py-1 rounded !transition hover:!bg-second hover:!text-light" href="{{ url("/products/{$location->slug}") }}">{{ $location->title }}</a>
                @endforeach
            </div>
            <div class="flex justify-between items-center pt-2 !mt-auto">
                <div class="flex">
                  @foreach ($product->prepareRatingImages() as $image)
                    <span><img src="{{ $image }}" alt="Star"></span>
                  @endforeach
                </div>
                <div class="commends">
                    <span>{{ $product->reviews_count }} Reviews</span>
                </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="swiper-button-next">
        <svg xmlns="http://www.w3.org/2000/svg"
            width="40" height="40" viewBox="0 0 40 40" fill="none">
            <g opacity="0.6">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M20.4173 4.5835C11.904 4.5835 5.00065 11.4852 5.00065 20.0002C5.00065 28.5135 11.904 35.4168 20.4173 35.4168C28.9306 35.4168 35.834 28.5135 35.834 20.0002C35.834 11.4852 28.9307 4.5835 20.4173 4.5835Z"
                    fill="#212121" stroke="#212121" stroke-width="1.5"
                    stroke-linecap="square" />
                <path d="M17 14L22.81 19.785L17 25.57" stroke="white"
                    stroke-width="1.5" stroke-linecap="round" />
            </g>
        </svg>
    </div>
    <div class="swiper-button-prev">
        <svg xmlns="http://www.w3.org/2000/svg"
            width="40" height="40" viewBox="0 0 40 40" fill="none">
            <g opacity="0.6">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M20.4173 4.5835C11.904 4.5835 5.00065 11.4852 5.00065 20.0002C5.00065 28.5135 11.904 35.4168 20.4173 35.4168C28.9306 35.4168 35.834 28.5135 35.834 20.0002C35.834 11.4852 28.9307 4.5835 20.4173 4.5835Z"
                    fill="#212121" stroke="#212121" stroke-width="1.5"
                    stroke-linecap="square" />
                <path d="M17 14L22.81 19.785L17 25.57" stroke="white"
                    stroke-width="1.5" stroke-linecap="round" />
            </g>
        </svg>
    </div>
  </div>
</div>

