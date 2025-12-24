@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      {{-- <img src="{{ url('/storage/images/home_filter.png') }}" alt="Баннер" style="max-width: 100%; height: auto; display: block; margin: 0 auto;"> --}}
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Your TrekGuider Password Reset Code</h2>
      <p>
        Hi {{ $user->getName() }},
        <br><br>
        We received a request to reset the password for your TrekGuider account.
        <br>
        Here's your code to set a new password:
      </p>
      <div style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px; font-size: 20px; letter-spacing: 2px;">
        {{ $code }}
      </div>
      <p>
          This code will expire in 1 hour for your security.
          If you didn't request this, you can safely ignore this email. If you have any security concerns, please reach out to our support team.
          <br>
          Best,
          The TrekGuider Team
      </p>
  </div>
@endsection