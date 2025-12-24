@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Important: Action Required for Your TrekGuider Account Verification</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          We're writing because there seems to be an issue with the verification of your TrekGuider account information. This might be due to details that couldn't be confirmed or need a bit more attention.
          <br><br>
          This could mean your account currently has some restrictions, like a temporary hold on payouts.
      </p>

      <p style="margin-top: 12px;">
        Please review and complete any outstanding verification steps in your Dashboard:
      </p>

      <a href="{{ $actionUrl ?: $user->makeProfileVerificationUrl() }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Review Verification Requirements
      </a>

      <p style="margin-top: 16px;">
          If you have any questions or need help, our support team is ready to assist.
          <br><br>
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

