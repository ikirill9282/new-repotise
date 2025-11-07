@extends('layouts.site')

@php
  $variables = $page?->variables ?? collect();
  $product = $product ?? null;
  $latestPayment = $latestPayment ?? null;
  $periodLabel = $periodLabel ?? null;
  $nextBillingDate = $nextBillingDate ?? null;
@endphp

@section('content')
  <section class="success_pay_block">
    @include('site.components.breadcrumbs', [
      'current_name' => 'Subscription'
    ])
    <div class="container">
      <div class="about_block">
        <div class="thanks_block">
          <div class="text_left">
            @include('site.components.heading', ['variables' => $variables->filter(fn($item) => str_contains($item->name, 'page'))])
            <p>{{ print_var('page_subtitle', $variables) ?? 'Your subscription is now active.' }}</p>
            <div class="block_view">
              <a
                href="{{ auth()->check() ? route('profile.purchases.subscriptions', ['type' => 'subscriptions']) : route('profile.purchases') }}"
                class="download {{ auth()->check() ? '' : 'open_auth' }}"
              >
                {{ print_var('left_button_text', $variables) ?? 'View subscriptions' }}
              </a>
              <a href="{{ $product?->makeUrl() ?? route('products') }}" class="view_purchas">
                {{ print_var('right_button_text', $variables) ?? 'Back to product' }}
              </a>
            </div>
          </div>
          <img src="{{ asset('assets/img/checked.png') }}" alt="" class="checked">
        </div>

        <div class="order_details">
          <h2>Subscription Details</h2>
          <div class="bottom_order_block">
            <div class="left_orders_group">
              <div class="title_block">
                <h3>{{ $product?->title ?? 'Subscription' }}</h3>
                <p>Plan <span>{{ $periodLabel ?? '—' }}</span></p>
              </div>
              <div class="items_group">
                <div class="item">
                  <img src="{{ $product?->preview?->image ?? asset('assets/img/checked.png') }}" alt="Preview" class="order_img">
                  <div class="description_orders">
                    <div class="title_description">
                      <h4>{{ $product?->title ?? 'Subscription' }}</h4>
                      <h5>{{ currency($latestPayment->amount ?? ($paymentIntent->amount / 100)) }} / {{ $periodLabel ?? 'Period' }}</h5>
                    </div>
                    @if($product)
                      @php
                        $productMeta = collect([
                          $product->types?->pluck('title')->implode(', '),
                          $product->locations?->pluck('title')->implode(', '),
                        ])->filter()->implode(', ');
                      @endphp
                      @if($productMeta)
                        <p>{{ $productMeta }}</p>
                      @endif
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="payment_block">
              <h2>Payment</h2>
              <div class="descriptions_group">
                <div class="descriptions_pay">
                  <p>Payment Method:</p>
                  <div class="right_text">
                    @php
                      $pmDetails = $paymentIntent->charges->data[0]->payment_method_details->card ?? null;
                    @endphp
                    @if($pmDetails)
                      <span>{{ ucfirst($pmDetails->brand) }} **** {{ $pmDetails->last4 }}</span>
                    @else
                      <span>{{ strtoupper($paymentMethod->type ?? '—') }}</span>
                    @endif
                  </div>
                </div>
                <div class="descriptions_pay">
                  <p>Subscription ID.</p>
                  <div class="right_text">
                    <span>#{{ $subscription->id }}</span>
                  </div>
                </div>
                <div class="descriptions_pay">
                  <p>Activated:</p>
                  <div class="right_text">
                    <span>{{ \Illuminate\Support\Carbon::parse($subscription->created_at)->format('H:i') }}</span>
                    <span>{{ \Illuminate\Support\Carbon::parse($subscription->created_at)->format('d.m.Y') }}</span>
                  </div>
                </div>
                <div class="descriptions_pay">
                  <p>Next billing:</p>
                  <div class="right_text">
                    <span>{{ optional($nextBillingDate)->format('d.m.Y') ?? '—' }}</span>
                  </div>
                </div>
                <div class="descriptions_pay">
                  <p>Status:</p>
                  <div class="right_text">
                    <span class="uppercase">{{ $subscription->stripe_status }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
@endsection
