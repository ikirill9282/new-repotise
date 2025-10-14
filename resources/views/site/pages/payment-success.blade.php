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
                        <a href="{{ url(print_var('left_button_link', $variables)) }}" class="download open_auth">{{ print_var('left_button_text', $variables) }}</a>
                        <a href="{{ route('profile.purchases') }}" class="view_purchas {{ auth()->check() ? '' : 'open_auth' }}">{{ print_var('right_button_text', $variables) }}</a>
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
                    <a href="{{ auth()->check() ? route('profile.referal') : url('/referal') }}" class="learn_more">
                      {{ print_var('share_button_text', $variables) }}
                    </a>
                </div>
                <div class="text_right {{ auth()->check() ? '' : '!w-full' }}">
                    <div class="input_block">
                        @if ($user)
                          <input type="text" value="{{ $user->makeReferalUrl() }}" readonly data-copyId="referal"/>
                          <button class="copyToClipboard" data-target="referal"><img src="{{ asset('assets/img/copy.svg') }}" alt=""></button>
                        @else
                          <x-btn outlined class="open_auth !w-full">Sign in</x-btn>
                        @endif
                    </div>
                    @if(auth()->check())
                      <div class="connecting">
                          <a href="{{ $user?->makeReferalUrl('FB') }}" class="hover:!text-blue-500 transition duration-500">
                              @include('icons.facebook')
                          </a>
                          <a href="{{ $user?->makeReferalUrl('PI') }}" class="hover:!text-rose-500 transition duration-500">
                              @include('icons.pinterest')
                          </a>
                          <a href="{{ $user?->makeReferalUrl('TW') }}" class="hover:!text-black transition duration-500">
                              @include('icons.twitter')
                          </a>
                          <a href="{{ $user?->makeReferalUrl('GM') }}" class="hover:!text-orange-500 transition duration-500">
                              @include('icons.mail')
                          </a>
                          <a href="{{ $user?->makeReferalUrl('WA') }}" class="hover:!text-emerald-500 transition duration-500">
                              @include('icons.whatsapp')
                          </a>
                          <a href="{{ $user?->makeReferalUrl('TG') }}" class="hover:!text-sky-500 transition duration-500">
                              @include('icons.telegram')
                          </a>
                      </div>
                    @endif
                </div>
            </div>
            <div class="order_details">
                <h2>Order Details</h2>
                <div class="bottom_order_block">
                    <div class="left_orders_group">
                        <div class="title_block">
                            <h3>Your order</h3>
                            <p>Items <span>({{ $order->products->count() }})</span></p>
                        </div>
                        <div class="items_group">
                            @foreach($order->products as $product)
                              <div class="item">
                                  <img src="{{ $product->preview->image }}" alt="Preview" class="order_img">
                                  <div class="description_orders">
                                      <div class="title_description">
                                          <h4><a href="{{ $product->makeUrl() }}" class="link-black">{{ $product->title }} x {{ $product->pivot->count }}</a></h4>
                                          <h5>
                                            @if ($order->type == 'sub')
                                              {{ currency($product->subprice->getPrice()) }} per {{ $order->sub_period }}
                                            @else
                                              {{ currency($product->getPrice()) }} 
                                              <span>{{ currency($product->getPriceWithoutDiscount()) }}</span>
                                            @endif
                                          </h5>
                                      </div>
                                      <p>
                                        @foreach($product->types as $type)
                                          {{ $type->title }}, 
                                        @endforeach
                                        @foreach($product->locations as $location)
                                          {{ $location->title }},
                                        @endforeach
                                      </p>
                                  </div>
                              </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="payment_block">
                        <h2>Payment</h2>
                        <div class="descriptions_group">
                            <div class="descriptions_pay">
                                <p>Payment Method: {{-- Card --}} </p> 
                                <div class="right_text">
                                    @php
                                      $pm = $pm_type = $paymentMethod->type;
                                      if ($pm_type == 'card') {
                                        $pm = $paymentMethod?->card->brand;
                                      }
                                    @endphp
                                    <span>{{ strtoupper($pm) }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Order Number.</p>
                                <div class="right_text">
                                    <a href="#">#{{ $order->id }}</a>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Date & Time:</p>
                                <div class="right_text">
                                    <span>{{ \Illuminate\Support\Carbon::parse($order->updated_at)->format('H:i') }}</span>
                                    <span>{{ \Illuminate\Support\Carbon::parse($order->updated_at)->format('d.m.Y') }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Subtotal:</p>
                                <div class="right_text">
                                    <span>{{ currency($order->getAmount()) }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Discount: </p>
                                <div class="right_text">
                                    <span class="{{ $order->getDiscount() > 0 ? '!text-emerald-500' : '' }}">-{{ currency($order->getDiscount()) }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p>Tax:</p>
                                <div class="right_text">
                                    <span class="color_red">{{ currency($order->getTax()) }}</span>
                                </div>
                            </div>
                            <div class="descriptions_pay">
                                <p class="color_black">Total:</p>
                                <div class="right_text">
                                    <span>{{ currency($order->getTotal()) }}</span>
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