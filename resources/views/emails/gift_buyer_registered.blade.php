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
      <h2 style="margin-top: 20px;">You Just Gifted Some Adventure! ("{{ $productName }}" Sent!)</h2>
      <p>
          Hi {{ $buyer->getName() }},
          <br><br>
          How thoughtful! You've successfully gifted "{{ $productName }}" through TrekGuider.
      </p>

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

      <p>
          We've sent an email to {{ $gift->recipient_email }} with all the details on how to claim their awesome new travel resource.
          You can also view your gift purchase details in the "My Purchases" section of your TrekGuider account.
      </p>

      <a href="{{ route('profile.purchases') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        View My Purchases
      </a>

      <p style="margin-top: 16px;">
          Thanks for sharing the spirit of exploration!<br>
          The TrekGuider Crew
      </p>
  </div>
@endsection
