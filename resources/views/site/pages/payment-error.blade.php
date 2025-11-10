@extends('layouts.site')

@php
  $variables = $page->variables;
  $errorInfo = $error ?? [
    'title' => 'Payment failed',
    'message' => 'We could not process your payment. Please try again later.',
    'push' => '',
    'code' => null,
  ];

  $currencyCode = data_get($summary, 'currency', 'USD');
  $formatCurrency = function ($value) use ($currencyCode) {
    if (is_null($value)) {
      return null;
    }

    $formatted = number_format((float) $value, 2, '.', ' ');

    switch (strtoupper($currencyCode)) {
      case 'USD':
        return '$' . $formatted;
      case 'EUR':
        return '€' . $formatted;
      case 'GBP':
        return '£' . $formatted;
      default:
        return strtoupper($currencyCode) . ' ' . $formatted;
    }
  };
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
                    @include('site.components.heading', [
                      'variables' => $variables->filter(fn($item) => str_contains($item->name, 'page')),
                      'header_text' => $errorInfo['title'] ?? print_var('page_header', $variables),
                    ])
                    <p>{{ $errorInfo['message'] ?? print_var('page_subtitle', $variables) }}</p>
                    @if(!empty($errorInfo['push']))
                      <span>{{ $errorInfo['push'] }}</span>
                    @endif
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
                            <p>Items <span>({{ data_get($summary, 'items', '--') }})</span></p>
                        </div>
                        <div class="items_group">
                            @if(!empty($cart['products'] ?? []))
                            @foreach ($cart['products'] as $product)
                                @include('site.components.cards.product', [
                                  'template' => 'cart',
                                  'model' => $product['model'],
                                  'count' => $product['count'],
                                ])
                            @endforeach
                            @else
                              <div class="empty_order_products">
                                <p>We couldn’t load order items for this payment attempt.</p>
                              </div>
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
                                <p>Payment Method:</p>
                                <div class="right_text">
                                    <span>{{ data_get($paymentDetails, 'method', 'Card payment') }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Order Number.</p>
                                <div class="right_text">
                                    <span>{{ data_get($paymentDetails, 'order_number', '—') }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Date & Time:</p>
                                <div class="right_text">
                                    <span>{{ data_get($paymentDetails, 'time', '--:--') }}</span>
                                    <span>{{ data_get($paymentDetails, 'date', '--') }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Subtotal:</p>
                                <div class="right_text">
                                    <span>{{ $summary ? $formatCurrency($summary['subtotal']) : '—' }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Discount: </p>
                                <div class="right_text">
                                    @php
                                      $hasDiscount = $summary && data_get($summary, 'discount', 0) > 0;
                                    @endphp
                                    <span class="{{ $hasDiscount ? 'color_red' : '' }}">
                                      @if($summary)
                                        {{ $hasDiscount ? '-' : '' }}{{ $formatCurrency(data_get($summary, 'discount', 0)) }}
                                      @else
                                        —
                                      @endif
                                    </span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Tax:</p>
                                <div class="right_text">
                                    <span>{{ $summary ? $formatCurrency(data_get($summary, 'tax', 0)) : '—' }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p class="color_black">Total:</p>
                                <div class="right_text">
                                    <span>{{ $summary ? $formatCurrency(data_get($summary, 'total', 0)) : '—' }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p class="color_black">Payment Status:</p>
                                <div class="right_text">
                                    <span class="color_red">{{ data_get($paymentDetails, 'status', $errorInfo['title']) }}</span>
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
