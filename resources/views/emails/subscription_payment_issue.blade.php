@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Heads Up: Payment Issue with Your "{{ $product->title }}" Subscription</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          We encountered a snag processing the payment for your "{{ $product->title }}" subscription on TrekGuider.
          <br>
          To keep your access uninterrupted, please take a moment to update your payment information in your Account Settings:
      </p>

      <a href="{{ route('profile.settings') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Update Payment Information
      </a>

      <p style="margin-top: 16px;">
          We'll try to process the payment again in a few days. If the issue isn't resolved, your subscription might be paused.
          <br><br>
          If you have any questions or need a hand, our support team is ready to help.
          <br><br>
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

