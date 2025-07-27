@extends('layouts.email')
@push('css')
  <style>
        * {
          box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            display: block;
        }
        .container {
            position: relative;
            z-index: 10;
            width: 100%;
            padding: 20px;
            margin: 0 auto;
            text-align: center;
            color: #fff;
            overflow: hidden;
        }

        .main-text {
          position: relative;
          z-index: 10;
          max-width: 768px;
          margin: 0 auto;
        }

        .promo-code {
            display: inline-block;
            background-color: #FC7361; /* активный синий цвет */
            color: #fff;
            font-weight: bold;
            font-size: 24px;
            padding: 10px 20px;
            border-radius: 6px;
            margin: 20px 0;
            letter-spacing: 2px;
            user-select: all;
        }
        .btn {
            display: inline-block;
            background-color: #FC7361; /* зелёная кнопка */
            color: #fff !important;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 30px;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #484134;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }

        .logo {
          position: absolute;
          width: 100%;
          height: 100%;
          top: 0;
          left: 0;
          z-index: 5;
        }

        .logo::after {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5);
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #999;
            text-align: center;
        }

        .wrap {
          max-width: 768px;
          margin: 0 auto;
          text-align: center;
        }

    </style>
@endpush

@section('content')
    <div class="container">
        <div class="logo">
          <img src="{{ asset('assets/img/bounty.jpg') }}" alt="Промо" class="header-image" />
        </div>
        <div class="main-text">
          <h2>Congratulations! </h2>

          <p class="">
            You have successfully received an exclusive promo code from trekguider.com — your trusted companion for organizing unforgettable trips and trekking routes.

            Use your promo code to get a discount on booking tours, purchasing gear, or other services on our website. Let your next adventure be even brighter and more affordable!

            Thank you for choosing trekguider.com. We wish you exciting travels and new discoveries!
          </p>
        </div>
        
    </div>
    <div class="wrap">

        <div class="promo-code">{{ $discount->code }}</div>

        <p>Simply apply your promo code at checkout to get a discount on tour bookings, gear purchases, and other services. Don’t miss the chance to make your next adventure even brighter and more affordable!</p>

        <a href="{{ route('products') }}" class="btn" target="_blank" rel="noopener">Begin travel</a>
    </div>
@endsection
