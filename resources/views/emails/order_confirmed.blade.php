@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  @php
    $orderNumber = str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
    $orderDate = ($order->updated_at ?? $order->created_at)?->timezone(config('app.timezone'));
    $orderDateLabel = $orderDate ? $orderDate->format('m.d.Y') : '';
  @endphp

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Your TrekGuider Order #{{ $orderNumber }} is Confirmed! (Access Your Goodies)</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Great news! Your TrekGuider order is confirmed, and your travel resources are ready.
      </p>

      <p style="margin: 0 0 8px 0;"><b>Order Number:</b> #{{ $orderNumber }}</p>
      @if($orderDateLabel)
        <p style="margin: 0 0 12px 0;"><b>Date:</b> {{ $orderDateLabel }}</p>
      @endif

      <p style="margin: 12px 0 6px 0;"><b>Your Items:</b></p>
      <ul style="margin-top: 0;">
        @foreach($order->order_products as $op)
          @php
            $openOrderId = \Illuminate\Support\Facades\Crypt::encryptString((string) $order->id);
            $openOrderProductId = \Illuminate\Support\Facades\Crypt::encryptString((string) $op->id);
            $accessUrl = route('profile.purchases', [
              'open_order_product_id' => $openOrderProductId,
              'open_order_id' => $openOrderId,
            ]);
          @endphp
          <li>
            {{ $op->product?->title ?? 'Product' }}
            —
            ${{ number_format((float) ($op->total ?? 0), 2) }}
            <br>
            <a href="{{ $accessUrl }}" style="color:#FC7361; text-decoration: underline;">
              Access Your Product
            </a>
          </li>
        @endforeach
      </ul>

      <p style="margin-top: 12px;">
        <b>Order Total:</b> ${{ number_format((float) ($order->getTotal() ?? 0), 2) }}
      </p>

      <a href="{{ route('profile.purchases') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Access Your Purchases
      </a>

      <p style="margin-top: 16px;">
          You can always find your order details and access your purchases under "My Purchases" in your TrekGuider account.
          <br><br>
          Questions about your order? Our support team is happy to help.
          <br><br>
          Happy exploring,<br>
          The TrekGuider Crew
      </p>
  </div>
@endsection

