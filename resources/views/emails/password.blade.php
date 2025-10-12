@extends('layouts.email')

@section('content')
  <div style="width: 100%; padding: 0; line-height: 0;">
    <img src="http://trekguider.com/assets/img/home_filter.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
  </div>

  <div style="padding: 20px;">
      <h2 style="margin-top: 20px;">Dear {{ $user->username }}</h2>
      <p>
        We are pleased to inform you that a password has been assigned to your account on {{ env('APP_NAME') }}.
        You can log in to your account using the following credentials:
      </p>
      <p style="text-align: left; max-width: 450px; margin: 0 auto;">
        Login: {{ $user->email }}
        <br>
        Password: {{ $password }}
      </p>
      <a href="{{ url('/?modal=auth') }}" style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
        Sign In
      </a>
      <p>
          For your security, we recommend changing this password after your first login. You can do this in your account settings.
      </p>
      <p>If you did not register on our site, please ignore this email.</p>
  </div>
@endsection