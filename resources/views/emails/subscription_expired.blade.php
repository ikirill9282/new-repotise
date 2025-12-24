@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
      <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Your TrekGuider Subscription to "{{ $product->title }}" Has Expired</h2>
      <p>
          Hi {{ $user->getName() }},
          <br><br>
          Just letting you know that your subscription to "{{ $product->title }}" on TrekGuider expired
          @if(!empty($expirationDateLabel))
            on <b>{{ $expirationDateLabel }}</b>.
          @else
            .
          @endif
      </p>

      <p>
          This usually happens if a subscription was canceled and not set to renew, or if we couldn't process a payment after a few tries.
          Your access to the content from this subscription has now ended.
      </p>

      <p>
          Want to jump back in? You can easily start a new subscription anytime:
      </p>

      <a href="{{ $product->makeUrl() }}"
         style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Resubscribe to "{{ $product->title }}"
      </a>

      <p style="margin-top: 16px;">
          If you think this is a mistake, please get in touch with our support team.
          <br><br>
          Best,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

