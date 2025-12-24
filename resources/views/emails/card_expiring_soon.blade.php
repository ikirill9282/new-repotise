@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Friendly Reminder: Your Card for TrekGuider Subscriptions is Expiring Soon</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Just a friendly heads-up: the payment card ending in <b>{{ $last4 }}</b>, which you use for your TrekGuider subscription(s), is set to expire soon.
      </p>

      <p>
          To make sure your subscriptions continue smoothly and without interruption, please update your payment information in your Account Settings before <b>{{ $expirationDateLabel }}</b>:
      </p>

      <a href="{{ route('profile.settings') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Update Your Card Details
      </a>

      <p style="margin-top: 16px;">
          A quick update now will keep your adventures going!
          <br><br>
          If you have any questions, just let us know.
          <br><br>
          Thanks,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

