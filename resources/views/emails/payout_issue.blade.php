@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Heads Up: Issue with Your Recent TrekGuider Payout</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          We're writing to let you know there was an issue processing your recent payout from TrekGuider.
      </p>

      <p style="margin: 0 0 12px 0;"><b>Reason:</b> {{ $reason }}</p>

      <p>
          Please take a moment to review your payout information in your TrekGuider Account Settings and make sure all details are accurate and up-to-date.
      </p>

      <a href="{{ $settingsUrl ?: route('profile.settings') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Review Your Payout Settings
      </a>

      <p style="margin-top: 16px;">
          If the issue continues or you need a hand, please reach out to TrekGuider support.
          <br><br>
          Sincerely,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

