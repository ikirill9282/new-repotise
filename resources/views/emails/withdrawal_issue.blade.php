@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Heads Up: Issue with Your TrekGuider Withdrawal Request</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          We encountered an issue while trying to process your recent withdrawal request from your TrekGuider account balance.
      </p>

      <p style="margin: 0 0 12px 0;"><b>Reason:</b> {{ $reason }}</p>

      <p>
          Please double-check your payout method details in your TrekGuider Account Settings and then try submitting the withdrawal request again.
      </p>

      <a href="{{ $settingsUrl ?: route('profile.settings') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Update Your Payout Methods
      </a>

      <p style="margin-top: 16px;">
          If you need further assistance, our support team is here to help.
          <br><br>
          Sincerely,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

