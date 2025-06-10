@extends('layouts.site')

@php
@endphp


@section('content')
  @if ($order?->products && $order->products->isNotEmpty())
    <section class="placing_order">
        @include('site.components.breadcrumbs', [
            'current_name' => 'Checkout',
        ])
        <div class="container">
            <div class="about_block">
                <div class="left_form">
                    <form action="/cart/order" >
                        <div class="input_block">
                            <input type="text" name="fullname" placeholder="Your Full Name" value="{{ auth()->check() ? auth()->user()->name : '' }}">
                            @include('icons.shield')
                        </div>
                        <div class="input_block">
                            <input type="email" name="email" placeholder="Your Email" value="{{ auth()->check() ? auth()->user()->email : '' }}">
                            @include('icons.shield')
                        </div>
                        <div class="menu_block">
                            <input type="hidden" name="is-gift" class="is-gift" value="0">
                            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link text-primary fw-semibold active position-relative is-gift-button"
                                        id="pills-home-tab" data-value="1" data-bs-toggle="pill" data-bs-target="#pills-home"
                                        type="button" role="tab" aria-controls="pills-home"
                                        aria-selected="true">For Myself</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link text-primary fw-semibold position-relative is-gift-button"
                                        id="pills-profile-tab" data-value="0" data-bs-toggle="pill" data-bs-target="#pills-profile"
                                        type="button" role="tab" aria-controls="pills-profile"
                                        aria-selected="false">Send as Gift</button>
                                </li>
                            </ul>
                        </div>
                        <div class="sections_tab">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                    aria-labelledby="pills-home-tab">

                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-profile-tab">
                                    <div class="input_block gift_input">
                                        <input type="email" placeholder="Gift Recipient Email">
                                        @include('icons.shield')
                                    </div>
                                    <div class="textarea_block">
                                        <textarea class="text-area-gift" placeholder="Add a Gift Message (Optional)"></textarea>
                                        @include('icons.shield')
                                        <span class="text-area-gift-counter"><span class="text-area-counter">0</span>/150</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="promo_cod">
                            <div class="input_block">
                                <input type="text" class="promocode-input" placeholder="Promo Code" value="{{ $order?->promocode->code  ?? ''}}">
                                @include('icons.shield')
                            </div>
                            <a href="#" class="apply">Apply</a>
                        </div>
                        <div id="payment"></div>
                        {{-- <div class="payment_methods">
                            <h3>Choose Payment Method</h3>
                            <label class="card-option">
                                <div class="left_text">
                                    <input type="radio" class="custom-radio" checked>
                                    <span class="label-text">Bank cards</span>
                                </div>
                                <div class="card-icons">
                                    <img src="{{ asset('/assets/img/cards.svg') }}" alt="Visa">
                                    <img src="{{ asset('/assets/img/cards1.svg') }}" alt="Amex">
                                    <img src="{{ asset('/assets/img/cards2.svg') }}" alt="MasterCard">
                                </div>
                            </label>
                            <label class="card-option">
                                <div class="left_text">
                                    <input type="radio" class="custom-radio" checked>
                                    <span class="label-text">Bank cards</span>
                                </div>
                                <div class="card-icons">
                                    <img src="{{ asset('/assets/img/cards.svg') }}" alt="Visa">
                                    <img src="{{ asset('/assets/img/cards1.svg') }}" alt="Amex">
                                    <img src="{{ asset('/assets/img/cards2.svg') }}" alt="MasterCard">
                                </div>
                            </label>
                        </div> --}}
                        <div class="costs">
                            <div class="text_cost">
                                <span>Subtotal</span>
                                <h4>$<span class="cart-subtotal">{{ number_format($order->getAmount()) }}</span></h4>
                            </div>
                            <div class="text_cost">
                                <span>Discount</span>
                                <h4 class="color_red">-$<span class="cart-discount">{{ number_format($order->getDiscount()) }}</span></h4>
                            </div>
                            <div class="text_cost">
                                <span>Tax</span>
                                <h4 class="color_red">-$<span class="cart-tax">{{ number_format($order->getTax()) }}</span></h4>
                            </div>
                            <div class="text_cost">
                                <h5>Total</h5>
                                <h6>$<span class="cart-total">{{ number_format($order->getTotal()) }}</span></h6>
                            </div>
                        </div>
                        <button class="place_button">Place Order</button>
                        <p class="terms_service">By placing your order, you agree to our <a href="{{ url('/all-policies') }}">Terms of
                                Service & Privacy Policy.</a></p>
                        <div class="bottom_back_block">
                            <a href="#" class="back_cart">
                                <svg xmlns="http://www.w3.org/2000/svg" width="7" height="12"
                                    viewBox="0 0 7 12" fill="none">
                                    <path d="M6 1L1 6L6 11" stroke="#A4A0A0" />
                                </svg>
                                Back to Cart
                            </a>
                            <a href="{{ url('/help-center') }}" class="need_help">Need Help?</a>
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
                            @include('site.components.cards.product', [
                              'template' => 'cart',
                              'model' => $product,
                            ])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
  @endif

  <div class="container empty-container !pt-10 !pb-10 {{ ($order?->products && $order->products->isNotEmpty()) ? 'hidden' : '' }}">
    @include('site.components.favorite.empty', [
      'text' => 'Cart',
      'class' => 'empty-cart',
    ])
  </div>
@endsection

@push('js')
  <script>
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
      applyPromocode($(this).siblings('.input_block').find('input').val());
    });

    $('.is-gift-button').on('click', function() {
      $('.is-gift').val($(this).data('value'));
    });

    $.ajax({
      url: '/api/payment/intent',
      method: 'POST',
      data: { _token: getCSRF() }
    }).then(response => {
      const client_secret = response.client_secret
      const stripe = Stripe('pk_test_51R4kScFkz2A7XNTioqDGOwaj9SuLpkVaOLCHhOfyGvq5iYdtJLPTju3OvoTCCS7tW7BdDR2xqes9mZdyQEbsEYeR00NHvVUfKl');
      const appearance = {
        theme: 'night'
      };

      const elements = stripe.elements({ 
        clientSecret: response.clientSecret,
        // ...appearance,
      });
      
      const paymentElement = elements.create('payment');
      paymentElement.mount("#payment");      

      console.log('opk');
      console.log(paymentElement);
      
    });
    
  </script>
@endpush