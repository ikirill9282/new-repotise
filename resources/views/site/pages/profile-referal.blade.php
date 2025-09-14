@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    <div class="the-content__content profile-referal-page">

      {{-- REFERAL STATE --}}
      <x-card class="mb-10">
        {{-- TITLE --}}
        <div class="rp-top__title">
          <h2>
            Referral Program
          </h2>
          <img src="{{ asset('assets/img/gift.png') }}" alt="Gift">
        </div>

        <div class="rp-top__body !gap-6 lg:!gap-3 xl:!gap-6 justify-between items-end flex-col lg:flex-row">
          
          {{-- BALANCE --}}
          <div class="rp-top__balance !w-full grow lg:max-w-lg">
            <div class="balance-input">
              <span class="inline-block !mb-2">
                Referral Balance
              </span>
              <div class="balance-input__input">
                <span>
                  {{ currency($user->funds()->where('group', 'referal')->sum('sum')) }}
                </span>
              </div>
            </div>
            <div x-data="{}" class="actions !gap-2">
              <x-btn x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'withdraw' })" class="!text-sm sm:!text-base referal-btn">Withdraw Funds</x-btn>
              <x-btn x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'funds' })" class="!text-sm sm:!text-base referal-btn" outlined>Add Funds</x-btn>
            </div>
          </div>

          {{-- REWARDS --}}
          <div class="rp-top__info flex-col sm:flex-row !w-full !gap-0 sm:!gap-12 lg:!gap-0 lg:flex-col xl:flex-row">
            <div class="col !grow-0 lg:!grow">
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
            <div class="col !grow-0 lg:!grow">
              <div class="progresss">
                <span>
                  Next Free Product Reward Progress:
                </span>
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
      </x-card>

      {{-- REFERAL TEXT --}}
      <x-card class=" mb-10">

        {{-- HEADING --}}
        <div class="rp-share__title">
          <h2>
            Earn Rewards by Sharing!
          </h2>
          <p>
            Share your link and start earning fantastic rewards! Here's how it works:
          </p>
        </div>

        {{-- CONTENT --}}
        <div class="rp-share__cols !flex-col !gap-4 md:!flex-row w-full">
          <div class="col !p-0 !flex-auto !w-full !border-0">
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

            <x-link href="{{ route('referal') }}" class="mt-2">Learn more</x-link>

          </div>
          <div class="w-[1px] bg-gray md:!mx-2"></div>

          <div class="col !p-0 !flex-auto !w-full !border-0 ">
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

            <x-link href="{{ route('sellers') }}" class="mt-2">Learn more</x-link>
          </div>
        </div>

        {{-- SOCIAL --}}
        <div class="rp-share__action !flex-col md:!flex-row !items-stretch md:!items-center">
          <div class="link md:basis-1/2">
            <span>
              Your Referral Link
            </span>
            <input type="hidden" value="{{ $user->makeReferalUrl() }}" data-copyId="referal" readonly />
            <div class="link-item break-all copyToClipboard" data-target="referal">
              {{ $user->makeReferalUrl() }}
            </div>
          </div>
          <div class="socials md:basis-1/2">
            <ul class="flex-wrap justify-between max-w-sm !w-full">
              <li class="!mr-0 max-w-10 sm:max-w-12 lg:max-w-none">
                <a href="{{ $user->makeReferalUrl('FB') }}" target="_blank" class="transition !text-second hover:!text-blue-600">
                  @include('icons.facebook', ['class' => 'max-w-full'])
                </a>
              </li>
              <li class="!mr-0 max-w-10 sm:max-w-12 lg:max-w-none">
                <a href="{{ $user->makeReferalUrl('PI') }}" class="transition !text-second hover:!text-rose-600" target="_blank">
                  @include('icons.pinterest', ['class' => 'max-w-full'])
                </a>
              </li>
              <li class="!mr-0 max-w-10 sm:max-w-12 lg:max-w-none">
                <a href="{{ $user->makeReferalUrl('TW') }}" target="_blank" class="transition !text-second hover:!text-gray-900">
                  @include('icons.twitter', ['class' => 'max-w-full'])
                </a>
              </li>
              <li class="!mr-0 max-w-10 sm:max-w-12 lg:max-w-none">
                <a href="{{ $user->makeReferalUrl('GM') }}" class="transition !text-second hover:!text-orange-600" target="_blank">
                  @include('icons.mail', ['class' => 'max-w-full'])
                </a>
              </li>
              <li class="!mr-0 max-w-10 sm:max-w-12 lg:max-w-none">
                <a href="{{ $user->makeReferalUrl('WA') }}" class="transition !text-second hover:!text-emerald-600" target="_blank">
                  @include('icons.whatsapp', ['class' => 'max-w-full'])
                </a>
              </li>
              <li class="!mr-0 max-w-10 sm:max-w-12 lg:max-w-none">
                <a href="{{ $user->makeReferalUrl('TG') }}" class="transition !text-second hover:!text-sky-600" target="_blank">
                  @include('icons.telegram', ['class' => 'max-w-full'])
                </a>
              </li>
            </ul>
          </div>
        </div>
      </x-card>

      {{-- TABLE --}}
      @livewire('profile.tables.profile-referal')
    </div>
  </x-profile.wrap>    
@endsection