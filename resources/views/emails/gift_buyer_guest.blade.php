@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  @php
    $productName = $order->order_products->first()?->product?->title
      ?? $order->products->first()?->title
      ?? 'your gift';
    $orderDate = ($order->updated_at ?? $order->created_at)?->timezone(config('app.timezone'));
    $orderDateLabel = $orderDate ? $orderDate->format('m.d.Y') : '';
  @endphp

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Welcome to TrekGuider &amp; Your Gift of "{{ $productName }}" is Sent!</h2>
      <p>
          Hi there,
          <br><br>
          Welcome to TrekGuider, and thank you for choosing us to gift "{{ $productName }}"! How thoughtful of you.
          <br><br>
          We've created a TrekGuider account for you using this email address ({{ $buyer->email }}) so you can easily manage your purchases and explore more.
      </p>

      <p style="margin-top: 12px;">
        First, let's set up your password to access your new account:
      </p>

      <a href="{{ route('home') }}?modal=set-password&email={{ urlencode($buyer->email) }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Set Your Password &amp; Access Your Account
      </a>

      <p style="margin-top: 16px;"><b>About your gift:</b></p>
      <p style="margin: 0 0 8px 0;"><b>Gifted Item:</b> {{ $productName }}</p>
      <p style="margin: 0 0 8px 0;"><b>Sent To:</b> {{ $gift->recipient_email }}</p>
      <p style="margin: 0 0 8px 0;"><b>Amount:</b> ${{ number_format((float) ($order->getTotal() ?? 0), 2) }}</p>
      @if($orderDateLabel)
        <p style="margin: 0 0 12px 0;"><b>Date:</b> {{ $orderDateLabel }}</p>
      @endif

      @if(!empty($order->recipient_message))
        <p style="margin-top: 12px;"><b>Your Message:</b></p>
        <div style="margin: 8px 0 18px; padding: 12px 14px; border: 1px solid #FC7361; border-radius: 6px;">
          "{{ $order->recipient_message }}"
        </div>
      @endif

      <p style="margin-top: 16px;">
        Once you set your password, you'll be able to log in, view your gift purchase in "My Purchases," and we'll also notify you by email as soon as the recipient claims their gift.
        <br><br>
        Adventure awaits,<br>
        The TrekGuider Crew
      </p>
  </div>
@endsection
