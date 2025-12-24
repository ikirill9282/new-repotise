@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Your TrekGuider Payout Method Confirmation Code</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          To securely add your new payout method on TrekGuider, please use this confirmation code:
      </p>

      <div style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; border-radius: 4px; font-size: 20px; letter-spacing: 2px;">
        {{ $code }}
      </div>

      <p style="margin-top: 16px;">
          This extra step helps keep your account and payout information safe.
          <br><br>
          If you didn't request to add a new payout method, please ignore this email. If you have any security concerns, contact our support team right away.
          <br><br>
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

