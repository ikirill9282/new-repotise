@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    <div class="the-content__content">
      <div class="rp-top">
        <div class="rp-top__title">
          <h2>
            Referral Program
          </h2>
          <img src="{{ asset('assets/img/gift.png') }}" alt="Gift">
        </div>
        <div class="rp-top__body">
          <div class="rp-top__balance">
            <div class="balance-input">
              <span>
                Referral Balance
              </span>
              <div class="balance-input__input">
                <span>
                  {{ currency($user->funds()->where('group', 'referal')->sum('sum')) }}
                </span>
              </div>
            </div>
            <div class="actions">
              <button class="main-btn">Withdraw Funds</button>
              <button class="main-btn trans-btn">Add Funds</button>
            </div>
          </div>
          <div class="rp-top__info">
            <div class="col">
              <p>
                <span>Referred Users:</span>
                <b>{{ $user->referals()->count() }}</b>
              </p>
              <p>
                <span>Discount Codes Earned:</span>
                <b>{{ $user->referal_codes()->count() }}</b>
              </p>
              <p>
                <span>Free Products Unlocked:</span>
                <b>{{ $user->referal_free_products()->count() }}</b>
              </p>
              <p>
                <span>Referral Income Earned:</span>
                <b>{{ currency($user->referal_income()->sum('sum')) }}</b>
              </p>
            </div>
            <div class="col">
              <div class="progresss">
                <span>
                  Next Free Product Reward Progress:
                </span>
                <!-- здесь просто указать от 0 до 10 с шагом по 1 -->
                <div data-progress="3" class="progresss-item">
                  @php
                    $referals_count = $user->referal_buyers()->count();
                    $delimeter = $referals_count > 0 ? ($referals_count % 10) : 0;
                  @endphp
                  @for($i = 0; $i <= 10; $i++)
                    <i class="{{ $i <= $delimeter ? 'active' : '' }} {{ $i == $delimeter ? 'last' : '' }}">
                      <span class="active-percent">{{ $i == $delimeter ? ($i > 0 ? $i : '')."0%" : '' }}</span>
                    </i>
                  @endfor
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="rp-share">
        <div class="rp-share__title">
          <h2>
            Earn Rewards by Sharing!
          </h2>
          <p>
            Share your link and start earning fantastic rewards! Here's how it works:
          </p>
        </div>
        <div class="rp-share__cols">
          <div class="col">
            <h3>
              Earn Rewards by Sharing!
            </h3>
            <ul>
              <li>


                <p>
                  <span>15% Discount Codes: </span> Give friends 15% off (up to $50) & get a code too!
                </p>
              </li>
              <li>
                <p>
                  <span> Free Product Rewards: </span> Get a free product (up to $50 value, or $25 from sellers) for
                  every 10 buyer
                  referrals!
                </p>
              </li>

            </ul>
            <a href="{{ route('referal') }}">Learn more</a>

          </div>
          <div class="col">
            <h3>
              Refer Sellers & Earn 25% Commission Share!
            </h3>
            <ul>
              <li>
                <p>
                  <span> 25% of Our Seller Commission - For You! </span> Earn 25% of platform commission from
                  referred seller
                  earnings for the first 30 days!
                </p>
              </li>
              <li>
                <p>
                  <span> Continued Income - 12.5% Commission Share! </span> Keep earning 12.5% of platform
                  commission from referred
                  seller earnings for the next 11 months!
                </p>
              </li>

            </ul>
            <a href="{{ route('sellers') }}">Learn more</a>

          </div>
        </div>
        <div class="rp-share__action">
          <div class="link basis-1/2">
            <span>
              Your Referral Link
            </span>
            <input type="hidden" value="{{ $user->makeReferalUrl() }}" data-copyId="referal" readonly />
            <div class="link-item break-all copyToClipboard" data-target="referal">
              {{ $user->makeReferalUrl() }}
            </div>
          </div>
          <div class="socials basis-1/2">
            <ul class="flex-wrap gap-3">
              <li class="!mr-0">
                <a href="{{ $user->makeReferalUrl('FB') }}" target="_blank" class="transition !text-gray-700 hover:!text-blue-600">
                  @include('icons.facebook')
                </a>
              </li>
              <li class="!mr-0">
                <a href="{{ $user->makeReferalUrl('PI') }}" class="transition !text-gray-700 hover:!text-rose-600" target="_blank">
                  @include('icons.pinterest')
                </a>
              </li>
              <li class="!mr-0">
                <a href="{{ $user->makeReferalUrl('TW') }}" target="_blank" class="transition !text-gray-700 hover:!text-gray-900">
                  @include('icons.twitter')
                </a>
              </li>
              <li class="!mr-0">
                <a href="{{ $user->makeReferalUrl('GM') }}" class="transition !text-gray-700 hover:!text-orange-600" target="_blank">
                  @include('icons.mail')
                </a>
              </li>
              <li class="!mr-0">
                <a href="{{ $user->makeReferalUrl('WA') }}" class="transition !text-gray-700 hover:!text-emerald-600" target="_blank">
                  @include('icons.whatsapp')
                </a>
              </li>
              <li class="!mr-0">
                <a href="{{ $user->makeReferalUrl('TG') }}" class="transition !text-gray-700 hover:!text-sky-600" target="_blank">
                  @include('icons.telegram')
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>


      <div class="rp-table">
        <div class="rp-table__head">
          <div class="head-user">
            Referred User
          </div>
          <div class="head-reff">
            Referral Date
          </div>
          <div class="head-type">
            Referral Type
          </div>
          <div class="head-status">
            Status
          </div>
          <div class="head-promo">
            Promo Codes
          </div>
          <div class="head-comm">
            Commission Earned
          </div>
        </div>
        <div class="rp-table__empty">
          <p>
            No referrals to display yet. Start sharing your referral link!
          </p>
        </div>
      </div>
    </div>
  </x-profile.wrap>    
@endsection