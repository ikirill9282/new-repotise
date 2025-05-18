@extends('layouts.site')

@php
  $variables = $page->variables;
@endphp

@section('content')
  <section class="success_pay_block">
    @include('site.components.breadcrumbs', [
      'current_name' => 'Success'
    ])
    <div class="container">
        <div class="about_block">
            <div class="thanks_block">
                <div class="text_left">
                    @include('site.components.heading', ['variables' => $variables->filter(fn($item) => str_contains($item->name, 'page'))])
                    <p>{{ print_var('page_subtitle', $variables) }}</p>
                    <div class="block_view">
                        <a href="{{ url(print_var('left_button_link', $variables)) }}" class="download">{{ print_var('left_button_text', $variables) }}</a>
                        <a href="{{ url(print_var('right_button_link', $variables)) }}" class="view_purchas">{{ print_var('right_button_text', $variables) }}</a>
                    </div>
                </div>
                <img src="{{ asset('assets/img/checked.png') }}" alt="" class="checked">
            </div>
            <div class="share_earn_block">
                <div class="left_text">
                    @include('site.components.heading', ['variables' => $variables->filter(fn($item) => str_contains($item->name, 'share'))])
                    <div class="share_text">
                      {!! print_var('share_text', $variables) !!}
                    </div>
                    <a href="{{ url(print_var('share_button_link', $variables)) }}" class="learn_more">{{ print_var('share_button_text', $variables) }}</a>
                </div>
                <div class="text_right">
                    <div class="input_block">
                        <input type="text" value="https://ru.freepik.com" readonly/>
                        <button><img src="{{ asset('assets/img/copy.svg') }}" alt=""></button>
                    </div>
                    <div class="connecting">
                        <a href="#" class="hover:!text-blue-500 transition duration-500">
                            @include('icons.facebook')
                        </a>
                        <a href="#" class="hover:!text-rose-500 transition duration-500">
                            @include('icons.pinterest')
                        </a>
                        <a href="#" class="hover:!text-black transition duration-500">
                            @include('icons.twitter')
                        </a>
                        <a href="#" class="hover:!text-pink-500 transition duration-500">
                            @include('icons.mail')
                        </a>
                        <a href="#" class="hover:!text-emerald-500 transition duration-500">
                            @include('icons.whatsapp')
                        </a>
                        <a href="#" class="hover:!text-cyan-500 transition duration-500">
                            @include('icons.telegram')
                        </a>
                    </div>
                </div>
            </div>
            <div class="order_details">
                <h2>Order Details</h2>
                <div class="bottom_order_block">
                    <div class="left_orders_group">
                        <div class="title_block">
                            <h3>Your order</h3>
                            <p>Items <span>(10)</span></p>
                        </div>
                        <div class="items_group">
                            <div class="item">
                                <img src="{{ asset('assets/img/order.png') }}" alt="" class="order_img">
                                <div class="description_orders">
                                    <div class="title_description">
                                        <h4>A Guide to Getting to Know North Korea</h4>
                                        <h5>$20 <span>$40</span></h5>
                                    </div>
                                    <p>Hiking, North Korea</p>
                                    <div class="counter">
                                        <button class="btn minus">−</button>
                                        <span class="count">1</span>
                                        <button class="btn plus">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <img src="{{ asset('assets/img/order.png') }}" alt="" class="order_img">
                                <div class="description_orders">
                                    <div class="title_description">
                                        <h4>A Guide to Getting to Know North Korea</h4>
                                        <h5>$20 <span>$40</span></h5>
                                    </div>
                                    <p>Hiking, North Korea</p>
                                    <div class="counter">
                                        <button class="btn minus">−</button>
                                        <span class="count">1</span>
                                        <button class="btn plus">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <img src="{{ asset('assets/img/order.png') }}" alt="" class="order_img">
                                <div class="description_orders">
                                    <div class="title_description">
                                        <h4>A Guide to Getting to Know North Korea</h4>
                                        <h5>$20 <span>$40</span></h5>
                                    </div>
                                    <p>Hiking, North Korea</p>
                                    <div class="counter">
                                        <button class="btn minus">−</button>
                                        <span class="count">1</span>
                                        <button class="btn plus">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="payment_block">
                        <h2>Payment</h2>
                        <div class="descriptions_group">
                            <div class="descriptions_pay">
                                <p>Payment Method: Credit Card</p>
                                <div class="right_text">
                                    <span>Tinkoff pay</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Order Number.</p>
                                <div class="right_text">
                                    <a href="#">#3891274219474986231984</a>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Date & Time:</p>
                                <div class="right_text">
                                    <span>09:00</span>
                                    <span>03.30.2025</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Subtotal:</p>
                                <div class="right_text">
                                    <span>$5,200</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Discount: </p>
                                <div class="right_text">
                                    <span class="color_red">-$200</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Tax:</p>
                                <div class="right_text">
                                    <span class="color_red">-$2</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p class="color_black">Total:</p>
                                <div class="right_text">
                                    <span>$5000</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p class="color_black">Payment Status:</p>
                                <div class="right_text">
                                    <span class="color_green">Successful</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="also-block">
      <div class="container">
        @include('site.components.recomend.wrapper', [
            'models' => \App\Models\Product::getAnalogs(),
            'card' => 'product',
            'wrap_class' => 'also_like',
            'header' => 'You May Also Like',
          ])
      </div>
    </div>
  </section>
@endsection