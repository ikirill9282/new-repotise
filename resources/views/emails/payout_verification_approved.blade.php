@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">You're Verified! Your TrekGuider Account is Ready for Payouts!</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Fantastic news! Your full verification for receiving payouts on TrekGuider is complete.
      </p>

      <p style="margin-top: 12px;">
        You can now:
      </p>
      <ul style="margin-top: 6px;">
        <li>Receive payouts for your earnings as a Creator.</li>
        <li>Continue selling without payout holds related to verification.</li>
      </ul>

      <p style="margin-top: 12px;">
        You can review your payout status any time in your Dashboard:
      </p>

      <a href="{{ $dashboardUrl ?: route('profile.dashboard') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Go to Dashboard
      </a>

      <p style="margin-top: 16px;">
          Thanks for helping keep TrekGuider secure for everyone.
          <br><br>
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

