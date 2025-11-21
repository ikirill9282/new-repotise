@php
  $trending_products = auth()->check()
      ? auth()->user()->getRecomendProducts(10)
      : \App\Models\Product::where('status_id', \App\Enums\Status::ACTIVE)
          ->whereNotNull('published_at')
          ->limit(10)
          ->latest()
          ->get();
  $cartService = new \App\Services\Cart();
@endphp

<style>
  .cart-modal .recommendations-scroll::-webkit-scrollbar {
      width: 6px;
  }
  .cart-modal .recommendations-scroll::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 9999px;
  }
  .cart-modal .recommendations-scroll::-webkit-scrollbar-thumb {
      background: #FC7361;
      border-radius: 9999px;
  }
  .cart-modal .recommendations-scroll {
      scrollbar-width: thin;
      scrollbar-color: #FC7361 #f1f1f1;
  }
  .cart-modal .item .img_products {
      width: 153px;
      height: 160px;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      padding: 0;
      border-radius: 5px;
  }
  .cart-modal .item .img_products .main_img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 5px;
  }
  .cart-modal .item .img_products > a {
      display: block;
      width: 100%;
      height: 100%;
  }
  .cart-modal .item .img_products .cart-modal-add-btn {
      position: absolute;
      left: calc(5%);
      bottom: 0px;
      /* transform: translateX(-50%); */
      width: calc(88%);
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
  }
  .cart-modal .item .product-title {
      font-size: 16px !important;
      font-weight: 500 !important;
      color: #1f1f1f !important;
  }
  .cart-modal h3 {
      font-size: 16px !important;
  }
  .cart-modal .cart-modal-title {
      width: 153px;
      text-align: center;
      margin: 0;
  }
  .cart-modal h4,
  .cart-modal h5,
  .cart-modal h6 {
      font-size: 16px !important;
  }
  @media (min-width: 768px) {
      .cart-modal .item .img_products {
          width: 153px;
          height: 160px;
          padding: 0;
      }
  }
  .cart-modal .item .cost p,
  .cart-modal .item .cost span {
      color: #A4A0A0 !important;
  }
  .cart-modal .item .cost span {
      color: #C0BCBC !important;
  }
</style>

<div class="flex flex-col md:flex-row-reverse justify-between items-stretch gap-2 cart-modal md:min-w-3xl rounded-lg select-none h-full md:h-[85vh] max-h-[90vh]">
  <div class="md:basis-1/4 order-2 md:!order-1 relative md:!pr-2 h-full">
      <div class="md:max-w-[280px] md:rounded-lg h-full flex flex-col">
          <h3 class="font-semibold mb-3 cart-modal-title">More to Explore</h3>
          <div class="products_item flex flex-col gap-3 pr-2 md:pr-0 flex-1 overflow-y-auto recommendations-scroll">
              @foreach ($trending_products as $product)
                  @php
                      $preview = $product->preview?->image
                          ? url($product->preview->image)
                          : asset('assets/img/default_avatar.png');
                      $title = strlen($product->title) > 50
                          ? trim(mb_substr($product->title, 0, 50)) . '...'
                          : $product->title;
                      $productHash = \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $product->id]);
                      $productKey = \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $product->id]);
                      $inCart = $cartService->inCart($product->id);
                      $isOwner = auth()->check() && (int) $product->user_id === auth()->id();
                  @endphp
                  <div class="item flex flex-col gap-2">
                      <div class="img_products relative rounded-lg overflow-hidden">
                          <a href="{{ $product->makeUrl() }}" class="block">
                              <img src="{{ $preview }}" alt="Preview of {{ $title }}" class="main_img object-cover w-full h-full">
                          </a>
                          @if (! $inCart && ! $isOwner)
                              <x-btn
                                  type="button"
                                  class="add-to-cart cart-modal-add-btn to_basket"
                                  data-hide-on-add="true"
                                  data-refresh-only="true"
                                  data-value="{{ $productHash }}"
                                  data-key="{{ $productKey }}"
                              >
                                  Add to cart
                              </x-btn>
                          @endif
                      </div>
                      <h4 class="product-title">
                          <a href="{{ $product->makeUrl() }}" class="transition !text-inherit hover:!text-black block text-ellipsis overflow-hidden whitespace-nowrap">
                              {{ $title }}
                          </a>
                      </h4>
                      <div class="cost text-[#A4A0A0] flex items-center gap-2">
                          <span class="!text-[#A4A0A0]">{{ currency($product->getPrice()) }}</span>
                          @if($product->getPriceWithoutDiscount())
                              <span class="!text-[#C0BCBC] line-through">{{ currency($product->getPriceWithoutDiscount()) }}</span>
                          @endif
                      </div>
                  </div>
              @endforeach
          </div>
      </div>
    </div>
    <div class=" md:basis-3/4 bg-white pt-4 px-2 pb-2 md:p-2 
                rounded-lg flex flex-col transition overflow-y-auto h-full
                cart-order recommendations-scroll
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
            <p class="terms_service !m-0 !p-0">By placing your order, you agree to our <a href="{{ url('/policies/privacy-policy') }}">Terms of
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