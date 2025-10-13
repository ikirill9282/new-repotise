<div>

    @push('head')
      <script src="https://js.stripe.com/v3/"></script>
    @endpush

    @if ($order?->products && $order->products->isNotEmpty())
        <section class="placing_order">
            @include('site.components.breadcrumbs', [
                'current_name' => 'Checkout',
            ])
            <div class="container">
              <div class="about_block">
                <div class="left_form">
                    {{-- @dump($this->form) --}}
                    <form action="/cart/order" id="payment-form1">

                        <div class="!mb-3">
                          <x-form.input 
                            wire:model="form.fullname" 
                            name="fullname"
                            placeholder="Your Full Name"
                            tooltipText='Enter your valid full name. e.g. "John Doe".'
                          />
                        </div>
                        
                        <div class="!mb-3">
                            <x-form.input 
                              wire:model="form.email"
                              name="email"
                              type="email"
                              placeholder="Your email"
                              tooltipText="Enter your valid email. We will send you validation link."
                            />
                        </div>
                        <div class="menu_block">
                            <ul class="nav nav-pills" id="" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <input wire:model="form.gift" type="checkbox" name="is-gift" value="true"
                                        id="gift-true" class="is-gift w-0 h-0">
                                    <label
                                        class="nav-link text-primary fw-semibold position-relative is-gift-button {{ $this->form['gift'] ? '' : 'active' }}"
                                        id="pills-home-tab" for="gift-true" data-value="0" data-bs-toggle="pill"
                                        data-bs-target="#pills-home" type="button" role="tab"
                                        aria-controls="pills-home"
                                        aria-selected="{{ $this->form['gift'] ? 'false' : 'true' }}">
                                        For Myself
                                    </label>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <input wire:model="form.gift" type="checkbox" name="is-gift" value="false"
                                        id="gift-false" class="is-gift w-0 h-0">
                                    <label
                                        class="nav-link text-primary fw-semibold position-relative is-gift-button {{ $this->form['gift'] ? 'active' : '' }}"
                                        id="pills-profile-tab" for="gift-false" data-value="1" data-bs-toggle="pill"
                                        data-bs-target="#pills-profile" type="button" role="tab"
                                        aria-controls="pills-profile"
                                        aria-selected="{{ $this->form['gift'] ? 'true' : 'false' }}">
                                        Send as Gift
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="sections_tab">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade {{ $this->form['gift'] ? '' : 'active show' }}"
                                    id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

                                </div>
                                <div class="tab-pane fade  {{ $this->form['gift'] ? 'active show' : '' }}"
                                    id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                    <div class="input_block gift_input">
                                        <input wire:model="form.recipient" type="email" name="recipient"
                                            placeholder="Gift Recipient Email"
                                            class="@error('form.recipient') border !border-red-500 @enderror">
                                        <x-tooltip class="!opacity-100 !absolute top-4 right-2"
                                            message='Enter recipient email. We will send email notification about gift.'>
                                            @include('icons.shield')
                                        </x-tooltip>

                                        @error('form.recipient')
                                            <span class="inline-block text-red-500 !mt-2">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="textarea_block relative">
                                        <textarea wire:model="form.recipient_message"
                                            class="text-area-gift @error('form.recipient_message') border !border-red-500 @enderror" name="recipient_message"
                                            placeholder="Add a Gift Message (Optional)"></textarea>
                                        <x-tooltip class="!opacity-100 !absolute top-4 right-2"
                                            message='Add a Gift Message (Optional)'>
                                            @include('icons.shield', [
                                                'style' => 'position: static;',
                                            ])
                                        </x-tooltip>
                                        <span class="text-area-gift-counter"><span
                                                class="text-area-counter">{{ !empty($this->form['recipient_message']) ? strlen($this->form['recipient_message']) : 0 }}</span>/150</span>

                                        @error('form.recipient_message')
                                            <span
                                                class="inline-block w-full !font-normal !justify-start !text-base !text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="promo_cod flex-col !items-start !gap-3">
                            <div class="flex w-full gap-3">
                                <div class="input_block">
                                    <input wire:model="promocode" type="text"
                                        class="promocode-input grow @error('promocode') !border-red-500 @enderror"
                                        name="promocode" placeholder="Promo Code"
                                        value="{{ $order?->promocode->code ?? '' }}">
                                    <x-tooltip class="!opacity-100 !absolute top-5 right-2"
                                        message='If you know promocode, enter here.'>
                                        @include('icons.shield')
                                    </x-tooltip>
                                </div>
                                @if (empty($this->order->discount_id))
                                    <a wire:click.prevent="applyPromocode" href="#"
                                        class="apply">Apply</a>
                                @else
                                    <a wire:click.prevent="removePromocode" href="#"
                                        class="px-4 py-2 leading-9 rounded border !border-[#FC7361] !text-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] transition">Remove</a>
                                @endif
                            </div>
                            @error('promocode')
                                <span id='error-promo' class="text-red-500 grow">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="class="@if($order->cost <= 0) hidden @endif">
                          <div wire:ignore>
                              <div id="payment" class="mb-4"></div>
                          </div>
                        </div>

                        <div class="costs">
                            <div class="text_cost">
                                <span>Subtotal</span>
                                <h4>$<span class="cart-subtotal">{{ number_format($order->getAmount()) }}</span>
                                </h4>
                            </div>
                            <div class="text_cost">
                                <span>Discount</span>
                                <h4 class="{{ $order->getDiscount() > 0 ? '!text-emerald-500' : '' }}">-$<span
                                        class="cart-discount">{{ number_format($order->getDiscount()) }}</span>
                                </h4>
                            </div>
                            <div class="text_cost">
                                <span>Tax</span>
                                <h4 class="color_red">$<span
                                        class="cart-tax">{{ number_format($order->getTax()) }}</span></h4>
                            </div>
                            <div class="text_cost">
                                <h5>Total</h5>
                                <h6>$<span class="cart-total">{{ number_format($order->getTotal()) }}</span></h6>
                            </div>
                        </div>

                        <x-btn id="submit-btn" class="!max-w-none !mb-3">Confirm Payment</x-btn>

                        <p class="text-sm group !mb-6">By confirm your payment, you agree to our <x-link
                              class="!border-none group-has-[a]:!text-active"  
                              href="{{ url('/all-policies') }}"
                            >
                              Terms of Service & Privacy Policy.
                            </x-link>
                        </p>
                        <div class="bottom_back_block">
                            <x-link href="#" class="!border-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="7" height="12"
                                    viewBox="0 0 7 12" fill="none">
                                    <path d="M6 1L1 6L6 11" stroke="#A4A0A0" />
                                </svg>
                                Back to Cart
                            </x-link>
                            <x-link href="{{ url('/help-center') }}" class="!border-none">Need Help?</x-link>
                        </div>
                    </form>
                </div>
                <div class="right_orders">
                  <div class="title_block">
                      <h3>Your order</h3>
                      <p>Items <span>(<span class="cart-counter">{{ $order->getCount() }}</span>)</span></p>
                  </div>
                  <div class="items_group">
                      @foreach ($order->products as $product)
                        <div class="item">
                            <img src="{{ url($product->preview->image) }}" alt="Product Preview" class="order_img">
                          <div class="description_orders">
                          <div class="title_description">
                              <h4><a href="{{ $product->makeUrl() }}">{{ $product->title }}</a>
                              </h4>
                              <h5>
                                  {{ currency($product->getPrice()) }}
                                  @if (isset($product->sale_price))
                                      <span>{{ currency($product->getPriceWithoutDiscount()) }}</span>
                                  @endif
                              </h5>
                          </div>
                          <p>{{ $product->categories->pluck('title')->join(', ') }}</p>
                          <div class="w-full flex justify-between items-center">
                            <div class="counter-wire">
                                <button wire:click="decrementProductCount({{ $product->id }})"
                                    class="btn minus">−</button>
                                <span
                                    class="count">{{ $product->pivot->count ?? $product->pivot['count'] }}</span>
                                <button wire:click="incrementProductCount({{ $product->id }})"
                                    class="btn plus">+</button>
                            </div>
                            <div wire:click.prevent="dropProduct({{ $product->id }})"
                                class="drop cart-drop-wire hover:cursor-pointer" data-item="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px"
                                    viewBox="0 0 24 24" fill="transparent">
                                    <rect width="24" height="24" fill="white" />
                                    <path d="M5 7.5H19L18 21H6L5 7.5Z" stroke="currentColor"
                                        stroke-linejoin="round" />
                                    <path d="M15.5 9.5L15 19" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M12 9.5V19" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M8.5 9.5L9 19" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M16 5H19C20.1046 5 21 5.89543 21 7V7.5H3V7C3 5.89543 3.89543 5 5 5H8M16 5L15 3H9L8 5M16 5H8"
                                        stroke="currentColor" stroke-linejoin="round" />
                                    <script xmlns="" />
                                  </svg>
                                  </div>
                                </div>
                            </div>
                          </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </section>
    @else
      <div
        class="container empty-container !pt-10 !pb-10 {{ $order?->products && $order->products->isNotEmpty() ? 'hidden' : '' }}">
        @include('site.components.favorite.empty', [
          'text' => 'Cart',
          'class' => 'empty-cart',
        ])
      </div>
    @endif
  </div>


@script
  <script>
      const stripe = Stripe('pk_test_51R4kScFkz2A7XNTioqDGOwaj9SuLpkVaOLCHhOfyGvq5iYdtJLPTju3OvoTCCS7tW7BdDR2xqes9mZdyQEbsEYeR00NHvVUfKl');
      const clientSecret = '{{ $this->clientSecret }}';
      const elements = stripe.elements({clientSecret});
      const paymentMethod = elements.create('payment');

      if (document.getElementById('payment')) {
        paymentMethod.mount('#payment');
      }

      const btn = document.getElementById('submit-btn');
      btn.addEventListener('click', (evt) => {
        evt.preventDefault();
        $wire.checkValidtion().then(async response => {
          const { error, setupIntent } = await stripe.confirmSetup({
            elements,
            redirect: 'if_required',
          });
          
          if (error) {
            $wire.dispatch('toastError', [{message: error.message}]);
          } else {
            $wire.dispatch('makePayment', { pm_id: setupIntent.payment_method });
          }
        });
      });

      $('.text-area-gift').on('input', function(evt) {
          $('.text-area-gift-counter').find('.text-area-counter').html(evt.target.value.length);
      });

      $('.promocode-input').on('input', function(evt) {
          $(this).val(evt.target.value.replace(' ', '').toUpperCase());
      });

      $('.promocode-input').on('change', function(evt) {
          $(this).val(evt.target.value.replace(' ', '').toUpperCase());
      });

      $('.apply').on('click', function(evt) {
          evt.preventDefault();
          // applyPromocode($(this).siblings('.input_block').find('input').val(), '#error-promo');
      });

      $('.is-gift-button').on('click', function() {
          const input = $('.is-gift');
          const val = (Number(input.val()) === 0) ? true : false;
          // $wire.set('form.gift', val);
          // $wire.call('setGift', val);
      });
  </script>
@endscript