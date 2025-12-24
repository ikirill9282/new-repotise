@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Confirmation: Your Recurring Donation to {{ $seller->getName() }} Has Been Canceled</h2>
      <p>
          Hi {{ $donor->getName() }},
          <br><br>
          This email confirms that your recurring donation to {{ $seller->getName() }} on TrekGuider has been canceled.
          <br>
          No further payments will be processed for this donation.
      </p>

      <p>
          We, and {{ $seller->getName() }}, thank you sincerely for your past support.
          If you ever wish to set up a new donation in the future, you can easily do so from {{ $seller->getName() }}'s Creator page on TrekGuider.
      </p>

      <p style="margin-top: 16px;">
          All the best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

