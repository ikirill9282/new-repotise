@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Good News! Your TrekGuider Payouts Have Been Resumed</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Great news! The temporary pause on payouts from your TrekGuider account has been lifted, and your payouts are now back on track.
          <br><br>
          Any pending payouts will be processed according to our usual schedule.
      </p>

      <p style="margin-top: 16px;">
          Thank you for your patience!<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

