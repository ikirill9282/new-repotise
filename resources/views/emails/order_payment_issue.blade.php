@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  @php
    $orderNumber = isset($order) && $order ? str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) : null;
  @endphp

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">
        Heads Up: Payment Issue with Your TrekGuider Order{{ $orderNumber ? ' #' . $orderNumber : '' }}
      </h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          We had a little trouble processing the payment for your recent TrekGuider order{{ $orderNumber ? ' (#' . $orderNumber . ')' : '' }}.
          <br>
          Unfortunately, the transaction didn't go through. This can happen for a few reasons, like insufficient funds, an expired card, or a typo in the card details.
          <br><br>
          Could you please update your payment information or try a different method to complete your purchase?
      </p>

      <a href="{{ $checkoutHash ? route('profile.checkout', ['order' => $checkoutHash]) : route('profile.purchases') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Update Payment Info / Retry Purchase
      </a>

      <p style="margin-top: 16px;">
          If you're still running into issues or think this might be an error, it's a good idea to check with your bank or card provider. Of course, our support team is also here to assist.
          <br><br>
          We're here to help!<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

