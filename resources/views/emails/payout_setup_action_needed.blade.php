@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Action Needed: Set Up Your Payout Information on TrekGuider</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          To enable payouts for your earnings as a Creator on TrekGuider, our secure payment processing system requires some additional information (or an update to your existing details). This is a standard step to ensure everything is secure and compliant for your payouts.
      </p>

      <p style="margin-top: 12px;"><b>Here’s what’s needed to set up or update your payout details:</b></p>
      @if(!empty($requirements))
        <ul style="margin-top: 6px;">
          @foreach($requirements as $req)
            <li>{{ $req }}</li>
          @endforeach
        </ul>
      @else
        <ul style="margin-top: 6px;">
          <li>Complete your identity verification</li>
          <li>Confirm required account details</li>
        </ul>
      @endif

      <p style="margin-top: 12px;">
          Please log in to your TrekGuider account and go to your Dashboard to provide this information as soon as you can:
      </p>

      <a href="{{ $actionUrl ?: $user->makeProfileVerificationUrl() }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Set Up/Update Payout Information
      </a>

      @if(!empty($dueDateLabel))
        <p style="margin-top: 12px;">
          To avoid any delays or temporary holds on your payouts, please complete this by <b>{{ $dueDateLabel }}</b>.
        </p>
      @endif

      <p style="margin-top: 16px;">
          If you have any questions, our TrekGuider support team is ready to assist.
          <br><br>
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

