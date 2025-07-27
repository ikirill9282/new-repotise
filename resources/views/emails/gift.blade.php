@extends('layouts.email')

@push('css')
  <style>
    body {
      color: #000;
    }
    /* Reset styles for email clients */
    .relative {
      position: relative;
    }

    .mb-10 {
      margin-bottom: 10px;
    }

    .main {
      /* background-image: url('https://img.goodfon.ru/original/1920x1080/d/1c/smailiki-zheltye-shary-ulybki.jpg');
      background-size: cover;
      background-position: center center; */
      margin-bottom: 40px;
    }

    .bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #000;
      opacity: 0.5;
    }

    .text-primary { 
      color: #FC7361;
    }

    .main_text {
      padding: 60px 0;
      height: 100%;
      position: relative;
      z-index: 5;
    }

    .main_text p:first-child {
      margin-bottom: 25px;
      font-weight: bold;
    }

    .main_text p {
      line-height: 1.5;
    }

    .user-message {
      position: relative;
      max-width: 500px;
      margin: 0 auto;
      padding: 15px 20px;
      border: 1px solid #FC7361;
      border-radius: 4px;
      text-align: left;
      margin-bottom: 40px;
    }

    .user-message .bg {
      background: #FC7361;
      opacity: 0.25;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #000;
      opacity: 0.5;
    }

    .user-message_title {
      font-weight: bold;
      margin-bottom: 15px;
    }

    .credentials {
      margin-bottom: 40px;
    }

    .credentials p {
      margin-bottom: 10px;
      line-height: 1.5;
    }

    .btn {
      display: inline-block;
      padding: 15px 20px;
      font-size: 18px;
      font-weight: bold;
      border-radius: 4px;
      background: #FC7361;
      color: #fefefe !important;
      text-decoration: none;
    }

    .btn:active {
      color: #fefefe;
    }

    .btn:link {
      color: #fefefe;
    }

    .btn:visited {
      color: #fefefe;
    }
  </style>
@endpush

@section('content')
  <div class="">
    {{-- <img style="filter: brightness(50%);" src="https://img.goodfon.ru/original/1920x1080/d/1c/smailiki-zheltye-shary-ulybki.jpg" alt=""> --}}
    <div class="main relative">
      <div class="bg" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;
              background-color: rgba(0, 0, 0, 0.5);"></div>
      <div class="main_text">
        <p style="font-weight: bold; font-size: 24px; margin-bottom: 15px;">
          Your got a gift! From <span class="text-primary">{{ $order->user->getName() }}</span>.
        </p>
        <p>
          We are excited to inform you that you have successfully received a gift on our website. We hope this gift brings you joy and positive emotions!<br>
          If you have any questions or need assistance, please don't hesitate to contact our support team — we are always here to help.
        </p>
      </div>
    </div>

    <div class="user-message">
      <div class="bg"></div>
      <p class="user-message_title"><strong>Gift Message Inside:</strong><br/></p>
      <p class="">{{ $order->recipient_message }}</p>
    </div>

    @if(!empty($credentials))
      <div class="credentials">
        <p style="">
          <p>We are pleased to inform you that your account has been successfully created on our website.<br> You can now log in and start enjoying all the features and benefits available to our members.</p>
          <p>If you have any questions or need assistance with your account, please feel free to contact our support team — we are here to help!</p>
          <p style="">
            <span>Your password is <span class="text-primary"><strong>{{ $credentials['password'] }}</strong></span></span>
          </p>
        </p>
      </div>
    @endif

    <div class="button">
      <a class="btn" href="{{ route('gift', ['h' => \Illuminate\Support\Facades\Crypt::encrypt($order->id)]) }}">Claim Your Gift</a>
    </div>
  </div>
@endsection
