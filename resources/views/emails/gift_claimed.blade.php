@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  @php
    $productName = $order->order_products->first()?->product?->title
      ?? $order->products->first()?->title
      ?? 'your gift';
    $recipient = $gift->recipient_email;
  @endphp

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Great News! Your Gift of "{{ $productName }}" Has Been Claimed!</h2>
      <p>
          Hi {{ $buyer->getName() }},
          <br><br>
          Just wanted to let you know – your gift of "{{ $productName }}" to {{ $recipient }} has been successfully claimed!
      </p>

      @if($gift->claimed_at)
        <p style="margin: 0 0 12px 0;"><b>Claimed on:</b> {{ $gift->claimed_at->timezone(config('app.timezone'))->format('m.d.Y') }}</p>
      @endif

      <p style="margin-top: 16px;">
          We hope they love it and it sparks some amazing travels.
          <br><br>
          Thanks again for choosing TrekGuider to share the joy of discovery!<br>
          The TrekGuider Crew
      </p>
  </div>
@endsection
