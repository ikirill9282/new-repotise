@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Confirmation: Your Subscription to "{{ $product->title }}" Has Been Canceled</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          This email confirms that your subscription to "{{ $product->title }}" on TrekGuider has been canceled, as requested.
      </p>

      @if(!empty($endOfAccessDateLabel))
        <p>
          You'll still have full access to the content until the end of your current billing period on <b>{{ $endOfAccessDateLabel }}</b>.
          This can be found under "My Purchases" in your account.
        </p>
      @else
        <p>
          You'll still have full access to the content until the end of your current billing period. This can be found under "My Purchases" in your account.
        </p>
      @endif

      <p>
          Should you decide to come back, resubscribing is easy through your account or the product page.
      </p>

      <a href="{{ route('profile.purchases.subscriptions', ['type' => 'subscriptions']) }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Manage Subscription
      </a>

      <p style="margin-top: 16px;">
          We're sorry to see you go and hope to welcome you back to the adventure soon!
          <br><br>
          All the best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

