@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Your Recurring Donation Payment to {{ $seller->getName() }} Was Successful (Receipt)</h2>
      <p>
          Hi {{ $donor->getName() }},
          <br><br>
          Just a quick confirmation: your scheduled recurring donation payment to {{ $seller->getName() }} on TrekGuider was successful.
      </p>

      <p style="margin: 0 0 8px 0;"><b>Recipient:</b> {{ $seller->getName() }}</p>
      <p style="margin: 0 0 8px 0;"><b>Amount Paid:</b> {{ $amountPaidLabel }}</p>
      <p style="margin: 0 0 8px 0;"><b>For the period:</b> {{ $currentPeriodLabel }}</p>
      @if(!empty($nextPaymentDateLabel))
        <p style="margin: 0 0 12px 0;"><b>Next Payment Date:</b> {{ $nextPaymentDateLabel }}</p>
      @endif

      <p style="margin-top: 16px;">
          Thank you for your continued support – it helps {{ $seller->getName() }} keep the adventures coming!
          <br><br>
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

