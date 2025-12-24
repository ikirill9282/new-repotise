@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  @php
    $gifter = $order->buyer?->getName() ?? 'Someone';
    $productName = $order->order_products->first()?->product?->title
      ?? $order->products->first()?->title
      ?? 'your gift';
  @endphp

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Friendly Nudge: Your TrekGuider Gift from {{ $gifter }} is Waiting!</h2>
      <p>
          Hi there,
          <br><br>
          Just a little reminder that you have an unclaimed gift from {{ $gifter }} waiting for you on TrekGuider: "{{ $productName }}".
          <br><br>
          Don't let it gather dust! Click the link below to claim your travel goodies:
      </p>

      <a href="{{ route('gift', ['token' => $gift->token]) }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Claim Your Gift Now
      </a>

      <p style="margin-top: 16px;">
          If you've already claimed this, feel free to ignore this email.
          <br><br>
          Happy travels,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

