@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">You're In! Your "{{ $product->title }}" Subscription is Active!</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Welcome aboard! Your subscription to "{{ $product->title }}" on TrekGuider is now active and ready for you to explore.
      </p>

      <p style="margin: 0 0 8px 0;"><b>Product:</b> {{ $product->title }}</p>
      <p style="margin: 0 0 8px 0;"><b>Price:</b> {{ $priceLabel }} per {{ $periodLabel }}</p>
      @if(!empty($nextBillingDateLabel))
        <p style="margin: 0 0 12px 0;"><b>Next Billing Date:</b> {{ $nextBillingDateLabel }}</p>
      @endif

      <p>
          You can manage your subscription, view billing history, and access your content anytime through your TrekGuider Account Settings or under "My Purchases".
      </p>

      <a href="{{ route('profile.purchases.subscriptions', ['type' => 'subscriptions']) }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Manage Your Subscription
      </a>

      <p style="margin-top: 16px;">
          We're excited to have you with us!
          <br>
          The TrekGuider Crew
      </p>
  </div>
@endsection

