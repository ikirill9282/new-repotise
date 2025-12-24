@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Good News! Your TrekGuider Payout is On Its Way!</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Great news! A payout from your TrekGuider account has been sent and is currently being processed.
      </p>

      <p style="margin: 0 0 8px 0;"><b>Amount:</b> {{ $amountLabel }}</p>
      @if(!empty($expectedArrivalDateLabel))
        <p style="margin: 0 0 12px 0;"><b>Expected Arrival Date:</b> {{ $expectedArrivalDateLabel }} (Please note: this can vary depending on your bank's processing times.)</p>
      @endif

      <p style="margin-top: 12px;">
          Funds typically arrive in your bank account within a few business days, but it can occasionally take a bit longer.
      </p>

      <p style="margin-top: 16px;">
          Happy earnings!<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

