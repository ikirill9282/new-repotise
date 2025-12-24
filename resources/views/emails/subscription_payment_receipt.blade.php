@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Your TrekGuider Subscription Payment for "{{ $product->title }}" (Receipt)</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Just a quick note to confirm your recurring payment for the "{{ $product->title }}" subscription on TrekGuider was successful.
      </p>

      <p style="margin: 0 0 8px 0;"><b>Product:</b> {{ $product->title }}</p>
      <p style="margin: 0 0 8px 0;"><b>Amount Paid:</b> {{ $amountPaidLabel }}</p>
      <p style="margin: 0 0 8px 0;"><b>Billing Period:</b> {{ $billingPeriodLabel }}</p>
      @if(!empty($nextBillingDateLabel))
        <p style="margin: 0 0 12px 0;"><b>Next Billing Date:</b> {{ $nextBillingDateLabel }}</p>
      @endif

      <p>
          Your access to "{{ $product->title }}" continues without a hitch. You can view your full billing history in your Account Settings.
      </p>

      <a href="{{ route('profile.purchases.subscriptions', ['type' => 'subscriptions']) }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        View Billing History
      </a>

      <p style="margin-top: 16px;">
          Thanks for being a valued subscriber!
          <br>
          The TrekGuider Team
      </p>
  </div>
@endsection

