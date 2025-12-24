@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Reminder: Complete Full ID Verification to Keep Your Payouts Coming</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Congrats on reaching {{ $thresholdLabel }} in earnings on TrekGuider! To enable payouts, our payment system requires a full identity verification. This typically involves submitting a photo of your ID and a selfie – it's a standard security measure for all Creators receiving payments.
      </p>

      <p style="margin-top: 12px;">
          To ensure your payouts can be processed without a hitch, please complete this quick verification process in your Dashboard:
      </p>

      <a href="{{ $actionUrl ?: $user->makeProfileVerificationUrl() }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Complete Full ID Verification
      </a>

      <p style="margin-top: 16px;">
          If you have any questions, our TrekGuider support team is ready to help.
          <br><br>
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

