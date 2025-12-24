@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
    @if($monthlySupport)
      <h2 style="margin-top: 20px;">You're Supporting {{ $seller->getName() }} Monthly! (Recurring Donation Set Up)</h2>
      <p>
        Hi {{ $donor->getName() }},
        <br><br>
        Thank you for setting up a recurring monthly donation to support {{ $seller->getName() }} on TrekGuider! Your ongoing commitment is fantastic.
      </p>

      <p style="margin: 0 0 8px 0;"><b>Recipient:</b> {{ $seller->getName() }}</p>
      <p style="margin: 0 0 8px 0;"><b>Amount:</b> ${{ number_format($amount, 2) }} per month</p>
      @if(!empty($nextPaymentDateLabel))
        <p style="margin: 0 0 12px 0;"><b>Next Payment Date:</b> {{ $nextPaymentDateLabel }}</p>
      @endif

      <p>You can manage your recurring donations anytime from your TrekGuider Account Settings:</p>

      <a href="{{ route('profile.settings') }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Manage Your Donations
      </a>

      <p style="margin-top: 16px;">
        We, and {{ $seller->getName() }}, truly appreciate your continued support!
        <br>
        The TrekGuider Team
      </p>
    @else
      <h2 style="margin-top: 20px;">Thank You for Your Donation to {{ $seller->getName() }}! (Receipt)</h2>
      <p>
        Hi {{ $donor->getName() }},
        <br><br>
        Thank you so much for your generous donation to {{ $seller->getName() }} on TrekGuider! Your support is truly appreciated and makes a real difference.
      </p>

      <p style="margin: 0 0 8px 0;"><b>Recipient:</b> {{ $seller->getName() }}</p>
      <p style="margin: 0 0 8px 0;"><b>Amount:</b> ${{ number_format($amount, 2) }}</p>
      @if(!empty($donationDateLabel))
        <p style="margin: 0 0 12px 0;"><b>Date:</b> {{ $donationDateLabel }}</p>
      @endif

      <p>
        Your contribution helps {{ $seller->getName() }} continue to share their passion and create amazing travel content for everyone to enjoy.
      </p>

      <p style="margin-top: 16px;">
        With gratitude,<br>
        The TrekGuider Team
      </p>
    @endif
  </div>
@endsection

