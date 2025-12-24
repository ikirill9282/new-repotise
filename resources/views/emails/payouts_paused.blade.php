@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Important Notice: Your TrekGuider Payouts Are Temporarily Paused</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          This email is to inform you that payouts from your TrekGuider account have been temporarily paused.
          <br><br>
          This is usually due to a pending account review or an issue that requires your attention. Please check your account notifications or contact TrekGuider support for more details on why this has happened and what steps are needed to resolve it.
      </p>

      <p style="margin-top: 16px;">
          We're working to get this sorted for you as quickly as possible.
          <br><br>
          Sincerely,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

