@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  @php
    $gifter = $order->buyer?->getName() ?? 'Someone';
    $productName = $order->order_products->first()?->product?->title
      ?? $order->products->first()?->title
      ?? 'a TrekGuider product';
  @endphp

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Surprise! {{ $gifter }} Sent You a Gift on TrekGuider!</h2>
      <p>
          Hi there,
          <br><br>
          You've got a gift! {{ $gifter }} has thoughtfully sent you "{{ $productName }}" through TrekGuider.
      </p>

      @if(!empty($order->recipient_message))
        <p style="margin-top: 12px;"><b>{{ $gifter }} also sent this message along:</b></p>
        <div style="margin: 8px 0 18px; padding: 12px 14px; border: 1px solid #FC7361; border-radius: 6px;">
          "{{ $order->recipient_message }}"
        </div>
      @endif

      <p>
          Ready to unwrap your new travel guide? Click here to claim it:
      </p>

      <a href="{{ route('gift', ['token' => $gift->token]) }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Claim Your Gift
      </a>

      @if(!empty($isNewRecipient))
        <p style="margin-top: 16px;">
          If you're new to TrekGuider, we'll quickly guide you through setting up an account so you can access your gift. Once claimed, you can always find it in your "My Purchases" section.
        </p>
      @endif

      <p style="margin-top: 16px;">
          Enjoy your new adventure resource!<br>
          The TrekGuider Team
      </p>
  </div>
@endsection
