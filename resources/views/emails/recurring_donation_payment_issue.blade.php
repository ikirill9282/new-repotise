@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Heads Up: Payment Issue with Your Recurring Donation to {{ $seller->getName() }}</h2>
      <p>
          Hi {{ $donor->getName() }},
          <br><br>
          We encountered a little snag processing the payment for your recurring donation to {{ $seller->getName() }} on TrekGuider.
          <br>
          To ensure your support continues without interruption, please take a moment to update your payment information in your Account Settings:
      </p>

      <a href="{{ route('profile.settings') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Update Payment Information
      </a>

      <p style="margin-top: 16px;">
          If your payment details aren't updated, your recurring donation might be paused.
          <br><br>
          If you have any questions, our support team is here to help.
          <br><br>
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

