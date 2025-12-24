@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Your TrekGuider Withdrawal is Being Processed!</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Just letting you know that your request to withdraw funds from your TrekGuider account balance has been approved, and your payout is now being processed.
      </p>

      <p style="margin: 0 0 8px 0;"><b>Amount:</b> {{ $amountLabel }}</p>
      @if(!empty($destinationLabel))
        <p style="margin: 0 0 12px 0;"><b>Destination:</b> {{ $destinationLabel }}</p>
      @endif

      <p style="margin-top: 12px;">
          Please allow a few business days for the funds to appear in your account. Processing times can vary depending on your chosen payout method.
      </p>

      <p style="margin-top: 12px;">
          You can track the status in your TrekGuider account.
      </p>

      <p style="margin-top: 16px;">
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

