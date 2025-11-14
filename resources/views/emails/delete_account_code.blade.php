@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Confirm Your Account Deletion Request</h2>
      <p>
          You recently requested to delete your TrekGuider account.
          To confirm this request, enter the verification code below within the next 60 minutes.
      </p>
      <div style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px; font-size: 20px; letter-spacing: 2px;">
        {{ $code }}
      </div>
      <p style="margin-top: 20px;">
          Once confirmed, your account will be deactivated immediately and permanently deleted after 30 days unless you log back in to cancel the request.
      </p>
      <p>
          If you did not initiate this request, please ignore this email or contact our support team right away.
      </p>
      <p style="margin-top: 10px;">
          Stay safe,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

