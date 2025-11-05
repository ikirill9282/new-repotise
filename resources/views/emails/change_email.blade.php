@extends('layouts.email')

@section('content')
  <div style="padding: 20px;">
      <h2 style="margin-top: 0;">Confirm Your New Email Address</h2>
      <p style="margin: 16px 0;">
          Hi {{ $user->name ?? $user->username ?? 'there' }},
          we received a request to update the email address associated with your TrekGuider account.
      </p>
      <p style="margin: 16px 0;">
          Please click the button below to confirm your new email address. This link will expire in 60 minutes.
      </p>
      <p style="margin: 24px 0;">
          <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 12px 20px; background: #FC7361; color: #ffffff; text-decoration: none; border-radius: 4px;">
              Confirm Email Change
          </a>
      </p>
      <p style="margin: 16px 0;">
          If you did not request this change, you can safely ignore this email and your address will remain the same.
      </p>
      <p style="margin: 16px 0 0;">
          Safe travels,<br>
          The TrekGuider Team
      </p>
  </div>
@endsection

