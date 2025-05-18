@extends('layouts.site')

@php
  $variables = $page->variables;   
@endphp

@section('content')
  <section class="success_pay_block">
    @include('site.components.breadcrumbs', [
      'current_name' => 'Trouble',
    ])
    <div class="container">
        <div class="about_block">
            <div class="thanks_block failed_pay">
                <div class="text_left">
                    @include('site.components.heading', ['variables' => $variables->filter(fn($item) => str_contains($item->name, 'page'))])
                    <p>{{ print_var('page_subtitle', $variables) }}</p>
                    <div class="block_view">
                        <a href="{{ url(print_var('page_button_link', $variables)) }}" class="download">
                          {{ print_var('page_button_text', $variables) }}
                        </a>
                    </div>
                </div>
                <img src="{{ asset('/assets/img/error_pay.png') }}" alt="Payment Trouble" class="error_pay">
            </div>
            <div class="order_details">
                <div class="bottom_order_block">
                    <div class="left_orders_group">
                        <div class="title_block">
                            <h3>Your order</h3>
                            <p>Items <span>(10)</span></p>
                        </div>
                        <div class="items_group">
                            @if(isset($cart['products']))
                            @foreach ($cart['products'] as $product)
                                @include('site.components.cards.product', [
                                  'template' => 'cart',
                                  'model' => $product['model'],
                                  'count' => $product['count'],
                                ])
                            @endforeach
                            @endif
                            {{-- <div class="item">
                                <img src="img/order.png" alt="" class="order_img">
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
                                <img src="img/order.png" alt="" class="order_img">
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
                                <img src="img/order.png" alt="" class="order_img">
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
                            </div> --}}
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
                                    <span class="color_red">Card Declined</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="having_trouble">
                @include('site.components.heading', ['variables' => $variables->filter(fn($item) => str_contains($item->name, 'trouble'))])
                <a href="{{ url(print_var('trouble_button_link', $variables)) }}">{{ print_var('trouble_button_text', $variables) }}</a>
            </div>
        </div>
    </div>
  </section>
@endsection