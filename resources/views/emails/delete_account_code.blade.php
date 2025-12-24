@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Your TrekGuider Account Deletion Code</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          We've received your request to delete your TrekGuider account. To confirm this action, please use the following code:
      </p>
      <div style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px; font-size: 20px; letter-spacing: 2px;">
        {{ $code }}
      </div>
      <p style="margin-top: 20px;">
          Once you confirm with this code, your account and all associated data will be scheduled for permanent deletion in 30 days. You can cancel this deletion by simply logging back into your account anytime within these 30 days.
      </p>
      <p>
          If you didn't request this, or if you've changed your mind and don't want to use this code, please ignore this email. If you have security concerns, contact our support team immediately.
      </p>
      <p style="margin-top: 10px;">
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

