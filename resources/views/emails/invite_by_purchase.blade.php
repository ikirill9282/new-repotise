@extends('layouts.email')

@section('content')
  <h2 style="margin-top: 20px;">Welcome to TrekGuider!</h2>
  <p> 
    Hello {{ $user->name }}!
    <br>
    You have been invited to join **TrekGuider.com** — your ultimate guide for trekking adventures.
    <br>
    Your email address has been registered successfully.
  </p>
  <p>
    Your password is: <i style="display: inline-block; padding: 5px 10px; border-radius: 8px; background-color:rgba(252, 115, 97, 1); color: #fff;">{{ $password }}</i> 
  </p>
  <p>
    For your security, we recommend changing your password after your first login.  
    You can reset your password anytime using the link below:
  </p>
  <p>
    <a href="{{ url('/?modal=reset') }}" style="display: inline-block; padding: 5px 10px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px; font-size: 14px;">
        Reset Password
    </a>
  </p>
@endsection