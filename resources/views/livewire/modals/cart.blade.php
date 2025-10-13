@php
  $trending_products = auth()->check()
      ? auth()->user()->getRecomendProducts(10)
      : \App\Models\Product::limit(10)->latest()->get();
@endphp

<div class="flex flex-col md:flex-row-reverse justify-between items-stretch gap-2 cart-modal md:min-w-3xl rounded-lg 
            {{-- max-h-[97vh] overflow-y-scroll overflow-x-hidden md:overflow-hidden  --}}
            select-none
            ">
   <style>
      .swiper-scrollbar-drag {
          background: #FC7361 !important;
      }
  </style>
  <div class="md:basis-1/4 order-2 md:!order-1 relative md:!pr-2">
      <div id="cart-slider" class="md:max-w-[280px] h-full overflow-hidden md:rounded-lg relative">
          <div class="swiper-wrapper">
              @foreach ($trending_products as $product)
                  <div class="swiper-slide">
                      <a href="{{ $product->makeUrl() }}">
                          <div class="flex flex-col justify-start items-stretch h-full">
                              <div class="mb-0 md:mb-auto h-[180px] md:h-[140px] md:!mb-2 rounded-lg md:overflow-hidden">
                                  <img src="{{ url($product->preview->image) }}" alt="Preview"
                                      class="w-full h-full object-cover">
                              </div>
                              <div class="text-[#A4A0A0]">
                                  {{ (strlen($product->title) > 30) ? trim(mb_substr($product->title, 0, 30)).'...' : $product->title }}
                              </div>
                          </div>
                      </a>
                  </div>
              @endforeach
          </div>

          <div class="swiper-button-next hover:!cursor-pointer !z-40 md:!bottom-10 md:!top-auto md:!left-[50%] md:!right-auto md:translate-x-[-50%] md:!rotate-[90deg]">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"
                  class="hover:!cursor-pointer">
                  <g opacity="0.6">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M20.4173 4.5835C11.904 4.5835 5.00065 11.4852 5.00065 20.0002C5.00065 28.5135 11.904 35.4168 20.4173 35.4168C28.9306 35.4168 35.834 28.5135 35.834 20.0002C35.834 11.4852 28.9307 4.5835 20.4173 4.5835Z"
                          fill="#212121" stroke="#212121" stroke-width="1.5" stroke-linecap="square" />
                      <path d="M17 14L22.81 19.785L17 25.57" stroke="white" stroke-width="1.5"
                          stroke-linecap="round" />
                  </g>
              </svg>
          </div>
          <div class="swiper-button-prev hover:!cursor-pointer !block !z-40 md:!top-10 md:!left-[50%] md:!right-auto md:translate-x-[-50%] md:!rotate-[-90deg]">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"
                  class="hover:!cursor-pointer">
                  <g opacity="0.6">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M20.4173 4.5835C11.904 4.5835 5.00065 11.4852 5.00065 20.0002C5.00065 28.5135 11.904 35.4168 20.4173 35.4168C28.9306 35.4168 35.834 28.5135 35.834 20.0002C35.834 11.4852 28.9307 4.5835 20.4173 4.5835Z"
                          fill="#212121" stroke="#212121" stroke-width="1.5" stroke-linecap="square" />
                      <path d="M17 14L22.81 19.785L17 25.57" stroke="white" stroke-width="1.5"
                          stroke-linecap="round" />
                  </g>
              </svg>
          </div>
      </div>

      <div class="swiper-scrollbar swiper-pagination swiper-pagination-progressbar swiper-pagination-vertical hidden md:block md:!top-0 md:!right-0 md:!left-auto md:!bottom-auto">
      </div>
    </div>
    <div class=" md:basis-3/4 bg-white pt-4 px-2 pb-2 md:p-2 
                rounded-lg flex flex-col transition overflow-y-scroll
                cart-order
                "
        >
        <div class="order-view {{ ($this->order?->products && $this->order->products->isNotEmpty()) ? '' : 'hidden' }}">
            <div class="title_block px-2 py-4 flex justify-between items-center">
              <h3>Your order</h3>
              <p>Items <span class="text-gray">(<span class="cart-counter text-gray">{{ $this->getCart()->getCartCount() }}</span>)</span></p>
            </div>
            <div class="products_modal">
                <div class="items_group">
                    @foreach ($this->order->products as $product)
                        @include('site.components.cards.product', [
                            'template' => 'cart',
                            'model' => $product,
                        ])
                    @endforeach
                </div>
            </div>
            <div class="costs pt-4">
                <div class="text_cost">
                    <span>Subtotal</span>
                    <h4>$<span class="cart-subtotal">{{ $this->order->getAmount() }}</span></h4>
                </div>
                <div class="text_cost">
                    <span>Discount</span>
                    <h4 class="{{ $this->order->getDiscount() > 0 ? '!text-emerald-500' : '' }}">
                      -$<span class="cart-discount">{{ $this->order->getDiscount() }}</span>
                    </h4>
                </div>
                <div class="text_cost">
                    <span>Tax</span>
                    <h4 class="color_red">$<span class="cart-tax">{{ $this->order->getTax() }}</span></h4>
                </div>
                <div class="text_cost">
                    <h5>Total</h5>
                    <h6>$<span class="cart-total">{{ $this->order->getTotal() }}</span></h6>
                </div>
            </div>
            <a wire:click.prevent="moveCheckout" href="#" class="place_button inline-block">Place Order</a>
            <p class="terms_service !m-0 !p-0">By placing your order, you agree to our <a href="{{ url('/all-policies') }}">Terms of
                    Service & Privacy Policy.</a>
            </p>
        </div>
          
        <div class="container h-full flex justify-center items-center  empty-container {{ ($this->order?->products && $this->order->products->isNotEmpty()) ? 'hidden' : '' }}">
          @include('site.components.favorite.empty', [
            'text' => 'Cart',
            'class' => 'empty-cart',
          ])
        </div>
    </div>
</div>