@extends('layouts.site')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.min.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.querySelector('#phone');
            if (phoneInput) {
                const iti = window.intlTelInput(phoneInput, {
                    initialCountry: 'us',
                    preferredCountries: ['us', 'gb', 'ca'],
                    utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js'
                });
                
                const form = phoneInput.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        phoneInput.value = iti.getNumber();
                    });
                }
            }

            // Отслеживание отправки формы верификации
            const verificationForm = document.getElementById('verification-form');
            if (verificationForm) {
                verificationForm.addEventListener('submit', function(e) {
                    // Google Analytics 4 (если используется)
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'verification_form_submit', {
                            'event_category': 'Verification',
                            'event_label': 'Account Verification Form',
                            'value': 1
                        });
                    }

                    // Google Analytics Universal (если используется)
                    if (typeof ga !== 'undefined') {
                        ga('send', 'event', 'Verification', 'Form Submit', 'Account Verification');
                    }

                    // Facebook Pixel (если используется)
                    if (typeof fbq !== 'undefined') {
                        fbq('track', 'InitiateCheckout', {
                            content_name: 'Account Verification',
                            content_category: 'Verification'
                        });
                    }

                    // Console log для отладки
                    console.log('Verification form submitted');
                });
            }
        });
    </script>
@endpush

@section('content')
    <div class="the-content">
        <section class="verification-form__wrap">
              <div class="container">
                <div class="verification-form__row">
                    {{-- Левая колонка - поля ввода --}}
                    <div class="verification-form__left-col" style="flex: 1;">
                    @include('site.components.breadcrumbs', [
                        'breadcrumbs' => [
                            'Home' => route('home'),
                            'Profile' => route('profile'),
                            'Account Verification' => route('verify')
                        ]
                    ])
                    <div class="verification-form__title">
                            <h1>Account Verification</h1>
                        <p>
                            Please provide the following information to verify your account and enable payouts. This
                            process helps us ensure the security of your account and comply with tax regulations.
                                Your information is securely encrypted.
                        </p>
                    </div>
                    <div class="verification-form__form">
                          <form action="{{ url('/profile/verify') }}" method="POST" id="verification-form">
                              @csrf
                              
                                {{-- Общая ошибка при отправке --}}
                              @error('form')
                                    <div class="bg-[#FC7361] text-white w-full p-3 rounded-lg mb-4">
                                        We encountered an error during verification. Please review the form and try again, or contact support if the issue persists.
                                </div>
                              @enderror

                              @if(auth()->user()->verify()->where('type', 'stripe')->exists())
                                <a 
                                        class="block !bg-[#FC7361] hover:!bg-[#484134] text-white transition w-full mx-auto p-2 rounded-lg text-center mb-4"
                                  href="{{ auth()->user()->makeStripeVerificationUrl() }}"
                                >
                                  Continue Verification
                                </a>
                                <a 
                                  href="{{ url('/profile/verify/cancel') }}"
                                  class="block w-full !text-[#212529] p-2 mx-auto transition border rounded-lg text-center !border-[#212529]/50 hover:!text-[#FC7361] hover:!border-[#FC7361]"
                                >
                                  Cancel Verification
                                </a>
                              @else
                                    {{-- Раздел Personal Details --}}
                                    <h2>Personal Details</h2>

                                    {{-- Full Name --}}
                                    <div class="form-group w-full" data-field="full_name">
                                    <label class="req">
                                            <span class="label-name">Full Name</span>
                                        <input 
                                          type="text" 
                                          name="full_name" 
                                          autocomplete="off" 
                                          class="@error('full_name') error @enderror" 
                                          placeholder="Your legal first and last name"
                                          data-required="true"
                                                value="{{ old('full_name', $user->options->full_name ?? '') }}"
                                        >
                                        <i>
                                            <svg width="13.332031" height="16.000488" viewBox="0 0 13.332 16.0005"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path id="Vector"
                                                    d="M6.66 10C6.48 10 6.32 9.93 6.19 9.8C6.07 9.68 6 9.51 6 9.33C6.02 8.89 6.15 8.47 6.38 8.1C6.61 7.73 6.92 7.42 7.3 7.2C7.55 7.06 7.75 6.85 7.87 6.6C7.99 6.34 8.02 6.06 7.97 5.78C7.92 5.52 7.79 5.28 7.6 5.09C7.41 4.9 7.17 4.77 6.91 4.72C6.71 4.68 6.51 4.69 6.32 4.74C6.14 4.79 5.96 4.88 5.81 5C5.66 5.13 5.54 5.29 5.45 5.46C5.37 5.64 5.33 5.83 5.33 6.03C5.33 6.21 5.26 6.38 5.13 6.5C5.01 6.63 4.84 6.7 4.66 6.7C4.48 6.7 4.32 6.63 4.19 6.5C4.07 6.38 4 6.21 4 6.03C4 5.64 4.08 5.26 4.25 4.9C4.41 4.55 4.65 4.24 4.95 3.99C5.23 3.75 5.57 3.57 5.92 3.47C6.28 3.37 6.66 3.34 7.02 3.39C7.39 3.44 7.75 3.57 8.06 3.76C8.38 3.96 8.65 4.22 8.86 4.53C9.07 4.83 9.21 5.18 9.28 5.55C9.35 5.91 9.34 6.29 9.25 6.65C9.17 7.01 9.01 7.35 8.78 7.65C8.56 7.94 8.27 8.19 7.95 8.37C7.78 8.47 7.64 8.61 7.53 8.78C7.42 8.94 7.35 9.13 7.33 9.33C7.33 9.51 7.26 9.68 7.13 9.8C7.01 9.93 6.84 10 6.66 10ZM7.19 15.84C8.63 15.26 13.33 12.98 13.33 8.02L13.33 4.58C13.33 3.88 13.11 3.19 12.7 2.63C12.29 2.06 11.71 1.63 11.05 1.41L6.87 0.03C6.74 -0.02 6.59 -0.02 6.45 0.03L2.28 1.41C1.61 1.63 1.03 2.06 0.62 2.63C0.21 3.19 0 3.88 0 4.58L0 8.02C0 12.4 4.67 15.07 6.1 15.79C6.27 15.9 6.46 15.96 6.66 16C6.85 15.98 7.03 15.92 7.19 15.84ZM10.62 2.68C11.02 2.81 11.37 3.07 11.62 3.41C11.86 3.75 12 4.16 12 4.58L12 8.02C12 12.15 7.94 14.1 6.69 14.6C5.43 13.97 1.33 11.63 1.33 8.02L1.33 4.58C1.33 4.16 1.46 3.75 1.71 3.41C1.95 3.07 2.3 2.81 2.7 2.68L6.66 1.36L10.62 2.68ZM6.66 11.33C6.53 11.33 6.4 11.37 6.29 11.44C6.18 11.51 6.1 11.62 6.05 11.74C6 11.86 5.98 12 6.01 12.13C6.03 12.25 6.1 12.37 6.19 12.47C6.28 12.56 6.4 12.62 6.53 12.65C6.66 12.68 6.8 12.66 6.92 12.61C7.04 12.56 7.14 12.48 7.22 12.37C7.29 12.26 7.33 12.13 7.33 12C7.33 11.82 7.26 11.65 7.13 11.52C7.01 11.4 6.84 11.33 6.66 11.33Z"
                                                    fill="#A4A0A0" fill-opacity="1.000000" fill-rule="nonzero" />
                                            </svg>
                                        </i>
                                    </label>
                                    @error('full_name')
                                            <span class="text-red-500">Please enter your Full Name.</span>
                                    @enderror
                                </div>

                                    {{-- Address --}}
                                    <div class="form-group w-full address-section" data-field="address">
                                        <span class="label-name custom-label">
                                            Address
                                        </span>
                                        <div class="gap-[15px] md:gap-[30px] w-full flex flex-col md:flex-row justify-between items-stretch">
                                            <div class="form-group w-full flex flex-col">
                                        <label class="half req !w-full">
                                            <input 
                                              type="text" 
                                              name="street"
                                              class="@error('street') error @enderror" 
                                              placeholder="Street address"
                                              data-required="true"
                                                        value="{{ old('street', $user->options->street ?? '') }}"
                                            >
                                        </label>
                                        @error('street')
                                                    <span class="text-red-500">Please enter a valid Address.</span>
                                        @enderror
                                    </div>
                                    <div class="form-group w-full flex flex-col">
                                        <label class="half !w-full">
                                            <input 
                                                name="street2"
                                                type="text"
                                                class="@error('street2') error @enderror" 
                                                placeholder="Street address line 2" 
                                                        value="{{ old('street2', $user->options->street2 ?? '') }}"
                                              >
                                        </label>
                                        @error('street2')
                                            <span class="text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                        <div class="gap-[15px] md:gap-[30px] w-full flex flex-col md:flex-row justify-between items-stretch mt-3">
                                    <div class="w-full flex flex-col">
                                        <label class="half req !w-full">
                                            <input 
                                                type="text" 
                                                name="city" 
                                                class="@error('city') error @enderror"
                                                        placeholder="City" 
                                                data-required="true"
                                                        value="{{ old('city', $user->options->city ?? '') }}"
                                              >
                                        </label>
                                        @error('city')
                                                    <span class="text-red-500">Please enter a valid Address.</span>
                                        @enderror
                                    </div>
                                    <div class="w-full flex flex-col">
                                        <label class="half req !w-full">
                                            <input 
                                                name="state"
                                                type="text" 
                                                class="@error('state') error @enderror" 
                                                placeholder="State/Province" 
                                                data-required="true"
                                                        value="{{ old('state', $user->options->state ?? '') }}"
                                              >
                                        </label>
                                        @error('state')
                                                    <span class="text-red-500">Please enter a valid Address.</span>
                                        @enderror
                                    </div>
                                </div>
                                        <div class="gap-[15px] md:gap-[30px] w-full flex flex-col md:flex-row justify-between items-stretch mt-3">
                                    <div class="w-full flex flex-col">
                                        <label class="half req !w-full">
                                            <input 
                                              name="zip"
                                              type="text" 
                                              class="@error('zip') error @enderror" 
                                              placeholder="Zip code" 
                                              data-required="true"
                                                        value="{{ old('zip', $user->options->zip ?? '') }}"
                                            >
                                        </label>
                                        @error('zip')
                                                    <span class="text-red-500">Please enter a valid Address.</span>
                                        @enderror
                                    </div>
                                    <div class="w-full flex flex-col">
                                        <label class="half req !w-full">
                                            <input 
                                              type="text" 
                                              name="country"
                                              class="@error('country') error @enderror"
                                                        placeholder="Country" 
                                              data-required="true"
                                                        value="{{ old('country', $user->options->country ?? '') }}"
                                            >
                                        </label>
                                        @error('country')
                                                    <span class="text-red-500">Please enter a valid Address.</span>
                                        @enderror
                                    </div>
                                </div>
                                    </div>

                                    {{-- Date of Birth --}}
                                    @php
                                        $birthdayInputId = 'birthday-input-' . uniqid();
                                        $birthdayValue = old('birthday', $user->options->birthday ?? '');
                                        // Форматируем значение для отображения в формате MM/DD/YYYY если оно в формате YYYY-MM-DD
                                        if ($birthdayValue && preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdayValue)) {
                                            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $birthdayValue);
                                            $birthdayDisplayValue = $date->format('m/d/Y');
                                        } else {
                                            $birthdayDisplayValue = $birthdayValue;
                                        }
                                    @endphp
                                    <div class="form-group w-full" data-field="birthday">
                                        <label class="half req !w-full">
                                            <span class="label-name">Date of Birth</span>
                                            <div class="relative">
                                                <input 
                                                  type="text" 
                                                  id="{{ $birthdayInputId }}"
                                                  class="@error('birthday') error @enderror"
                                                  placeholder="MM/DD/YYYY" 
                                                  data-required="true"
                                                  value="{{ $birthdayDisplayValue }}"
                                                  style="padding-right: 40px; cursor: pointer;"
                                                  autocomplete="off"
                                                  readonly
                                                >
                                                @if($birthdayValue && preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdayValue))
                                                    <input type="hidden" name="birthday" id="birthday-hidden-{{ $birthdayInputId }}" value="{{ $birthdayValue }}">
                                                @else
                                                    <input type="hidden" name="birthday" id="birthday-hidden-{{ $birthdayInputId }}" value="">
                                                @endif
                                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" style="color: #A4A0A0;" id="calendar-icon-{{ $birthdayInputId }}">
                                                        @include('icons.calendar')
                                                    </div>
                                            </div>
                                        </label>
                                        @error('birthday')
                                            <span class="text-red-500">Please enter a valid Date of Birth.</span>
                                        @enderror
                                    </div>

                                    {{-- Phone Number (Optional) --}}
                                    <div class="form-group w-full" data-field="phone">
                                  <label>
                                            <span class="label-name">Phone Number <span style="font-weight: normal; color: #666;">(Optional)</span></span>
                                      <input 
                                          id="phone" 
                                          type="tel" 
                                          name="phone" 
                                                placeholder="Your phone number (optional)"
                                          class="@error('phone') error @enderror"
                                          data-input="phone"
                                                value="{{ old('phone', $user->options->phone ?? '') }}"
                                        >
                                  </label>
                                  @error('phone')
                                    <span class="text-red-500">{{ $message }}</span>
                                  @enderror
                                </div>

                                    {{-- Tax ID or Passport/ID Number --}}
                                    <div class="form-group w-full" data-field="tax_id">
                                        <label class="req">
                                            <span class="label-name">Tax ID or Passport/ID Number</span>
                                            <input 
                                                type="text" 
                                                name="tax_id"
                                                class="@error('tax_id') error @enderror"
                                                placeholder="Enter your Tax ID (SSN) or Passport/ID Number"
                                                value="{{ old('tax_id', $user->options->tax_id ?? '') }}"
                                                data-required="true"
                                                required
                                            >
                                        </label>
                                        @error('tax_id')
                                            <span class="text-red-500">Please enter a valid Tax ID or Passport/ID Number.</span>
                                        @enderror
                                    </div>
                              @endif

                                {{-- Social Media Verification --}}
                                <div class="form-group w-full" data-field="social">
                                    <h2>Social Media Verification (Recommended)</h2>
                                    <p style="margin-bottom: 20px;">
                                        To expedite and strengthen your account verification, we highly recommend linking
                                        your social media profiles.
                                    </p>
                                    <div class="profile-social-wrap">
                                        {{-- YouTube --}}
                                        <div class="social-item-wrapper">
                                            <label class="check-label">
                                                <div class="checkbox">
                                                    <div class="item">
                                                        <img src="{{ asset('assets/img/icons/youtube.svg') }}" alt="">
                                                        <span>
                                                            Youtube channel
                                                        </span>
                                                    </div>
                                                    <div class="checkbox-item {{ empty($user->options?->youtube ?? null) ? '' : 'active' }}">
                                                        <input type="checkbox" name="youtube_check" id="youtube_check" class="social-checkbox" data-social="youtube" {{ empty($user->options?->youtube ?? null) ? '' : 'checked' }}>
                                                        <span class="decor"></span>
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="social-url-input" id="youtube_url_wrapper" style="display: {{ empty($user->options?->youtube ?? null) ? 'none' : 'block' }}; margin-top: 10px;">
                                                <input 
                                                    type="url" 
                                                    name="youtube" 
                                                    id="youtube_url"
                                                    class="form-control @error('youtube') error @enderror" 
                                                    placeholder="https://youtube.com/@yourchannel"
                                                    value="{{ old('youtube', $user->options->youtube ?? '') }}"
                                                >
                                                @error('youtube')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- TikTok --}}
                                        <div class="social-item-wrapper">
                                            <label class="check-label">
                                                <div class="checkbox">
                                                    <div class="item">
                                                        <img src="{{ asset('assets/img/icons/tiktok.svg') }}" alt="">
                                                        <span>
                                                            TikTok Account
                                                        </span>
                                                    </div>
                                                    <div class="checkbox-item {{ empty($user->options?->tiktok ?? null) ? '' : 'active' }}">
                                                        <input type="checkbox" name="tiktok_check" id="tiktok_check" class="social-checkbox" data-social="tiktok" {{ empty($user->options?->tiktok ?? null) ? '' : 'checked' }}>
                                                        <span class="decor"></span>
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="social-url-input" id="tiktok_url_wrapper" style="display: {{ empty($user->options?->tiktok ?? null) ? 'none' : 'block' }}; margin-top: 10px;">
                                                <input 
                                                    type="url" 
                                                    name="tiktok" 
                                                    id="tiktok_url"
                                                    class="form-control @error('tiktok') error @enderror" 
                                                    placeholder="https://tiktok.com/@yourusername"
                                                    value="{{ old('tiktok', $user->options->tiktok ?? '') }}"
                                                >
                                                @error('tiktok')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Google --}}
                                        <div class="social-item-wrapper">
                                            <label class="check-label">
                                                <div class="checkbox">
                                                    <div class="item">
                                                        <img src="{{ asset('assets/img/icons/google.svg') }}" alt="">
                                                        <span>
                                                            Google Account
                                                        </span>
                                                    </div>
                                                    <div class="checkbox-item {{ empty($user->options?->google ?? null) ? '' : 'active' }}">
                                                        <input type="checkbox" name="google_check" id="google_check" class="social-checkbox" data-social="google" {{ empty($user->options?->google ?? null) ? '' : 'checked' }}>
                                                        <span class="decor"></span>
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="social-url-input" id="google_url_wrapper" style="display: {{ empty($user->options?->google ?? null) ? 'none' : 'block' }}; margin-top: 10px;">
                                                <input 
                                                    type="url" 
                                                    name="google" 
                                                    id="google_url"
                                                    class="form-control @error('google') error @enderror" 
                                                    placeholder="https://plus.google.com/yourprofile"
                                                    value="{{ old('google', $user->options->google ?? '') }}"
                                                >
                                                @error('google')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Facebook --}}
                                        <div class="social-item-wrapper">
                                            <label class="check-label">
                                                <div class="checkbox">
                                                    <div class="item">
                                                        <img src="{{ asset('assets/img/icons/facebook.svg') }}" alt="">
                                                        <span>
                                                            Facebook Account/Page
                                                        </span>
                                                    </div>
                                                    <div class="checkbox-item {{ empty($user->options?->facebook ?? null) ? '' : 'active' }}">
                                                        <input type="checkbox" name="facebook_check" id="facebook_check" class="social-checkbox" data-social="facebook" {{ empty($user->options?->facebook ?? null) ? '' : 'checked' }}>
                                                        <span class="decor"></span>
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="social-url-input" id="facebook_url_wrapper" style="display: {{ empty($user->options?->facebook ?? null) ? 'none' : 'block' }}; margin-top: 10px;">
                                                <input 
                                                    type="url" 
                                                    name="facebook" 
                                                    id="facebook_url"
                                                    class="form-control @error('facebook') error @enderror" 
                                                    placeholder="https://facebook.com/yourprofile"
                                                    value="{{ old('facebook', $user->options->facebook ?? '') }}"
                                                >
                                                @error('facebook')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Instagram --}}
                                        <div class="social-item-wrapper">
                                            <label class="check-label">
                                                <div class="checkbox">
                                                    <div class="item">
                                                        <img src="{{ asset('assets/img/icons/insta.svg') }}" alt="">
                                                        <span>
                                                            Instagram Account
                                                        </span>
                                                    </div>
                                                    <div class="checkbox-item {{ empty($user->options?->instagram ?? null) ? '' : 'active' }}">
                                                        <input type="checkbox" name="instagram_check" id="instagram_check" class="social-checkbox" data-social="instagram" {{ empty($user->options?->instagram ?? null) ? '' : 'checked' }}>
                                                        <span class="decor"></span>
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="social-url-input" id="instagram_url_wrapper" style="display: {{ empty($user->options?->instagram ?? null) ? 'none' : 'block' }}; margin-top: 10px;">
                                                <input 
                                                    type="url" 
                                                    name="instagram" 
                                                    id="instagram_url"
                                                    class="form-control @error('instagram') error @enderror" 
                                                    placeholder="https://instagram.com/yourusername"
                                                    value="{{ old('instagram', $user->options->instagram ?? '') }}"
                                                >
                                                @error('instagram')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- X (Twitter) --}}
                                        <div class="social-item-wrapper">
                                            <label class="check-label">
                                                <div class="checkbox">
                                                    <div class="item">
                                                        <img src="{{ asset('assets/img/icons/xai.svg') }}" alt="">
                                                        <span>
                                                            X (Twitter) Account
                                                        </span>
                                                    </div>
                                                    <div class="checkbox-item {{ empty($user->options?->xai ?? null) ? '' : 'active' }}">
                                                        <input type="checkbox" name="twitter_check" id="twitter_check" class="social-checkbox" data-social="twitter" {{ empty($user->options?->xai ?? null) ? '' : 'checked' }}>
                                                        <span class="decor"></span>
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="social-url-input" id="twitter_url_wrapper" style="display: {{ empty($user->options?->xai ?? null) ? 'none' : 'block' }}; margin-top: 10px;">
                                                <input 
                                                    type="url" 
                                                    name="twitter" 
                                                    id="twitter_url"
                                                    class="form-control @error('twitter') error @enderror" 
                                                    placeholder="https://x.com/yourusername"
                                                    value="{{ old('twitter', $user->options->xai ?? '') }}"
                                                >
                                                @error('twitter')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <p style="margin-top: 20px;">
                                        Don't see your social network listed? <a href="{{ route('help-center') }}" class="text-[#FC7361] hover:underline">Contact us</a> to verify other
                                        social media profiles manually.
                                    </p>
                                </div>

                              @if(!auth()->user()->verify()->where('type', 'stripe')->exists())
                                    {{-- Текст соглашения перед кнопкой --}}
                                    <div class="info" style="margin: 10px 0;">
                                        <span>
                                            By clicking 'Continue', you certify that the information provided is accurate and truthful. This data will be used for account verification, tax form generation, and you are providing your digital signature for these purposes. You also agree to our <a href="{{ url('/policies') }}">Terms of Service</a>, <a href="{{ url('/policies/privacy-policy') }}">Privacy Policy</a>, and <a href="{{ url('/policies') }}">Other Terms</a>.
                                        </span>
                                    </div>

                                    {{-- Кнопки --}}
                              <div class="action">
                                  <a 
                                    href="{{ url()->previous() }}" 
                                    class="back border rounded transition leading-10 px-4 
                                      !text-[#212529] 
                                      hover:!text-[#FC7361] 
                                      hover:!border-[#FC7361]"
                                      >
                                      Back
                                  </a>
                                  <button type="submit">
                                            Continue
                                  </button>
                              </div>
                              @endif
                            </form>
                              </div>
                    </div>

                    {{-- Правая колонка - пояснения (пустая обёртка для позиционирования) --}}
                    <div class="verification-form__sidebar-wrapper">
                        {{-- Пояснение для Full Name --}}
                        <div class="sidebar-item" data-field="full_name" style="display: none;">
                            <div class="sidebar-item-content">
                                <p style="font-size: 14px; line-height: 1.6; color: #666; margin: 0;">
                                    <strong style="color: #212529;">Full Name</strong><br>
                                    Enter your full legal name as shown on your government-issued ID, such as a passport or driver's license. This ensures accurate verification and tax reporting.
                                </p>
                            </div>
                        </div>

                        {{-- Пояснение для Address --}}
                        <div class="sidebar-item" data-field="address" style="display: none;">
                            <div class="sidebar-item-content">
                                <p style="font-size: 14px; line-height: 1.6; color: #666; margin: 0;">
                                    <strong style="color: #212529;">Address</strong><br>
                                    Provide your current, complete residential address. Accurate address information is essential for account verification and potential communication.
                                </p>
                            </div>
                        </div>

                        {{-- Пояснение для Date of Birth --}}
                        <div class="sidebar-item" data-field="birthday" style="display: none;">
                            <div class="sidebar-item-content">
                                <p style="font-size: 14px; line-height: 1.6; color: #666; margin: 0;">
                                    <strong style="color: #212529;">Date of Birth</strong><br>
                                    Please enter your date of birth for verification purposes.
                                </p>
                            </div>
                        </div>

                        {{-- Пояснение для Phone Number --}}
                        <div class="sidebar-item" data-field="phone" style="display: none;">
                            <div class="sidebar-item-content">
                                <p style="font-size: 14px; line-height: 1.6; color: #666; margin: 0;">
                                    <strong style="color: #212529;">Phone Number</strong><br>
                                    Providing a phone number is optional but highly recommended. It can be used to recover your account and for important communications.
                                </p>
                            </div>
                        </div>

                        {{-- Пояснение для Tax ID --}}
                        <div class="sidebar-item" data-field="tax_id" style="display: none;">
                            <div class="sidebar-item-content">
                                <p style="font-size: 14px; line-height: 1.6; color: #666; margin: 0;">
                                    <strong style="color: #212529;">Tax ID or Passport/ID Number</strong><br>
                                    For tax compliance, please enter your Tax Identification Number (TIN) if you are a US resident, or your Passport or National ID Number if you are a non-US resident. This information is securely processed for tax reporting.
                                </p>
                    </div>
                </div>

                        {{-- Пояснение для Social Media Verification --}}
                        <div class="sidebar-item" data-field="social" style="display: none;">
                            <div class="sidebar-item-content">
                                <p style="font-size: 14px; line-height: 1.6; color: #666; margin: 0;">
                                    <strong style="color: #212529;">Social Media Verification</strong><br>
                                    Linking your social media accounts significantly helps us verify your identity and can unlock additional platform features. This step is highly recommended for a smoother verification process.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Обработка кнопки Back
            const backButton = document.querySelector('.back');
            if (backButton) {
                backButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Используем history.back() для возврата на предыдущую страницу
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        // Если истории нет, переходим на главную страницу
                        window.location.href = '{{ route("home") }}';
                    }
                });
            }
            
            // Валидация формы перед отправкой
            const verificationForm = document.getElementById('verification-form');
            if (verificationForm) {
                verificationForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('Form validation started');
                    
                    // Удаляем предыдущие сообщения об ошибках
                    document.querySelectorAll('.field-error-message').forEach(el => el.remove());
                    document.querySelectorAll('input.error').forEach(el => el.classList.remove('error'));
                    
                    const requiredFields = [
                        { 
                            name: 'full_name', 
                            selector: 'input[name="full_name"]',
                            message: 'Please enter your Full Name.',
                            container: '[data-field="full_name"]'
                        },
                        { 
                            name: 'street', 
                            selector: 'input[name="street"]',
                            message: 'Please enter a valid Address.',
                            container: '.address-section'
                        },
                        { 
                            name: 'city', 
                            selector: 'input[name="city"]',
                            message: 'Please enter a valid Address.',
                            container: '.address-section'
                        },
                        { 
                            name: 'state', 
                            selector: 'input[name="state"]',
                            message: 'Please enter a valid Address.',
                            container: '.address-section'
                        },
                        { 
                            name: 'zip', 
                            selector: 'input[name="zip"]',
                            message: 'Please enter a valid Address.',
                            container: '.address-section'
                        },
                        { 
                            name: 'country', 
                            selector: 'input[name="country"]',
                            message: 'Please enter a valid Address.',
                            container: '.address-section'
                        },
                        { 
                            name: 'birthday', 
                            selector: 'input[name="birthday"][type="hidden"]',
                            message: 'Please enter a valid Date of Birth.',
                            container: '[data-field="birthday"]',
                            visibleSelector: '#{{ $birthdayInputId }}'
                        },
                        { 
                            name: 'tax_id', 
                            selector: 'input[name="tax_id"]',
                            message: 'Please enter a valid Tax ID or Passport/ID Number.',
                            container: '[data-field="tax_id"]'
                        }
                    ];
                    
                    let firstErrorField = null;
                    let isValid = true;
                    
                    requiredFields.forEach(field => {
                        let fieldElement = document.querySelector(field.selector);
                        let fieldValue = '';
                        
                        // Для birthday проверяем скрытое поле или видимое
                        if (field.name === 'birthday') {
                            if (fieldElement) {
                                fieldValue = fieldElement.value.trim();
                            }
                            // Если скрытого поля нет или оно пустое, проверяем видимое поле
                            if (!fieldValue && field.visibleSelector) {
                                const visibleInput = document.querySelector(field.visibleSelector);
                                if (visibleInput) {
                                    fieldValue = visibleInput.value.trim();
                                    // Если значение есть в видимом поле, но нет скрытого, создаем его
                                    if (fieldValue && !fieldElement) {
                                        const dateMatch = fieldValue.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
                                        if (dateMatch) {
                                            const year = dateMatch[3];
                                            const month = String(dateMatch[1]).padStart(2, '0');
                                            const day = String(dateMatch[2]).padStart(2, '0');
                                            fieldValue = `${year}-${month}-${day}`;
                                            
                                            fieldElement = document.createElement('input');
                                            fieldElement.type = 'hidden';
                                            fieldElement.name = 'birthday';
                                            fieldElement.id = 'birthday-hidden-{{ $birthdayInputId }}';
                                            fieldElement.value = fieldValue;
                                            visibleInput.parentElement.appendChild(fieldElement);
                                        }
                                    }
                                }
                            }
                        } else {
                            if (fieldElement) {
                                fieldValue = fieldElement.value.trim();
                            }
                        }
                        
                        // Проверяем, заполнено ли поле
                        if (!fieldValue) {
                            isValid = false;
                            
                            // Находим элемент для отображения ошибки
                            const container = document.querySelector(field.container);
                            let errorContainer = null;
                            let inputToHighlight = null;
                            
                            if (field.name === 'birthday') {
                                inputToHighlight = document.querySelector(field.visibleSelector);
                                if (container) {
                                    errorContainer = container.querySelector('label') || container;
                                }
                            } else {
                                inputToHighlight = fieldElement;
                                if (container) {
                                    const fieldWrapper = container.querySelector(field.selector);
                                    if (fieldWrapper) {
                                        errorContainer = fieldWrapper.closest('.form-group') || fieldWrapper.closest('label') || fieldWrapper.parentElement;
                                    } else {
                                        errorContainer = container;
                                    }
                                } else if (fieldElement) {
                                    errorContainer = fieldElement.closest('.form-group') || fieldElement.closest('label') || fieldElement.parentElement;
                                }
                            }
                            
                            // Добавляем класс error к полю
                            if (inputToHighlight) {
                                inputToHighlight.classList.add('error');
                            }
                            
                            // Создаем сообщение об ошибке
                            const errorMessage = document.createElement('span');
                            errorMessage.className = 'text-red-500 field-error-message';
                            errorMessage.style.display = 'block';
                            errorMessage.style.marginTop = '5px';
                            errorMessage.textContent = field.message;
                            
                            // Добавляем сообщение об ошибке
                            if (errorContainer) {
                                // Проверяем, нет ли уже сообщения об ошибке
                                const existingError = errorContainer.querySelector('.field-error-message');
                                if (!existingError) {
                                    errorContainer.appendChild(errorMessage);
                                }
                            } else if (inputToHighlight && inputToHighlight.parentElement) {
                                inputToHighlight.parentElement.appendChild(errorMessage);
                            }
                            
                            // Сохраняем первое поле с ошибкой для прокрутки
                            if (!firstErrorField) {
                                firstErrorField = inputToHighlight || fieldElement || container;
                            }
                        }
                    });
                    
                    // Если есть ошибки, прокручиваем к первому полю с ошибкой
                    if (!isValid && firstErrorField) {
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Фокусируемся на поле, если это input
                        if (firstErrorField.tagName === 'INPUT') {
                            setTimeout(() => {
                                firstErrorField.focus();
                                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }, 300);
                        }
                        return false;
                    }
                    
                    // Если все поля заполнены, отправляем форму
                    this.submit();
                });
            }
            
            // Обработка показа/скрытия полей URL для соцсетей
            const socialCheckboxes = document.querySelectorAll('.social-checkbox');
            socialCheckboxes.forEach(checkbox => {
                const socialName = checkbox.getAttribute('data-social');
                const urlWrapper = document.getElementById(socialName + '_url_wrapper');
                const urlInput = document.getElementById(socialName + '_url');
                
                // Находим родительский элемент checkbox-item для добавления класса active
                const checkboxItem = checkbox.closest('.checkbox-item');
                
                // Функция для обновления состояния
                function updateCheckboxState(isChecked) {
                    if (checkboxItem) {
                        if (isChecked) {
                            checkboxItem.classList.add('active');
                        } else {
                            checkboxItem.classList.remove('active');
                        }
                    }
                    
                    if (urlWrapper) {
                        if (isChecked) {
                            urlWrapper.style.display = 'block';
                            if (urlInput) {
                                urlInput.focus();
                            }
                        } else {
                            urlWrapper.style.display = 'none';
                            if (urlInput) {
                                urlInput.value = '';
                            }
                        }
                    }
                }
                
                // Инициализация состояния при загрузке
                updateCheckboxState(checkbox.checked);
                
                // Обработчик изменения чекбокса
                checkbox.addEventListener('change', function() {
                    updateCheckboxState(this.checked);
                });
                
                // Обработчик клика на label для переключения чекбокса
                const label = checkbox.closest('.check-label');
                if (label) {
                    label.addEventListener('click', function(e) {
                        // Если клик был не на самом чекбоксе, переключаем его
                        if (e.target !== checkbox && !checkbox.contains(e.target)) {
                            e.preventDefault();
                            checkbox.checked = !checkbox.checked;
                            updateCheckboxState(checkbox.checked);
                            // Триггерим событие change для других обработчиков
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                }
            });
            
            // Инициализация календаря для даты рождения
            const birthdayInputId = '{{ $birthdayInputId }}';
            let birthdayPicker = null;
            let initAttempts = 0;
            const maxAttempts = 100; // Увеличиваем количество попыток
            
            function initBirthdayDatePicker() {
                initAttempts++;
                
                const input = document.querySelector('#' + birthdayInputId);
                if (!input) {
                    if (initAttempts < maxAttempts) {
                        setTimeout(initBirthdayDatePicker, 100);
                    } else {
                        console.error('Birthday input element not found:', birthdayInputId);
                    }
                    return;
                }
                
                if (input.dataset.pickerInitialized === 'true') {
                    return; // Уже инициализирован
                }
                
                // Проверяем наличие AirDatepicker
                if (typeof window.AirDatepicker === 'undefined') {
                    if (initAttempts < maxAttempts) {
                        setTimeout(initBirthdayDatePicker, 100);
                    } else {
                        console.error('AirDatepicker library not loaded');
                    }
                    return;
                }
                
                // Пытаемся использовать createBirthdayDatePicker, если доступна
                if (typeof window.createBirthdayDatePicker === 'function') {
                    try {
                        birthdayPicker = window.createBirthdayDatePicker('#' + birthdayInputId);
                        if (birthdayPicker) {
                            input.dataset.pickerInitialized = 'true';
                            input._birthdayPicker = birthdayPicker;
                            setupCalendarHandlers();
                            console.log('Birthday date picker initialized using createBirthdayDatePicker');
                            return;
                        }
                    } catch (error) {
                        console.warn('Error using createBirthdayDatePicker, falling back to direct initialization:', error);
                    }
                }
                
                // Если функция недоступна, создаем календарь напрямую
                try {
                    const currentValue = input.value;
                    let initialDate = null;
                    
                    // Парсим текущее значение
                    if (currentValue) {
                        const dateMatch = currentValue.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                        if (dateMatch) {
                            initialDate = new Date(parseInt(dateMatch[1]), parseInt(dateMatch[2]) - 1, parseInt(dateMatch[3]));
                        } else {
                            const parts = currentValue.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
                            if (parts) {
                                initialDate = new Date(parseInt(parts[3]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                            } else {
                                const parsed = new Date(currentValue);
                                if (!isNaN(parsed.getTime())) {
                                    initialDate = parsed;
                                }
                            }
                        }
                    }
                    
                    // Находим или создаем скрытое поле
                    let hiddenInput = input.parentElement.querySelector('input[name="birthday"][type="hidden"]');
                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'birthday';
                        hiddenInput.id = 'birthday-hidden-' + birthdayInputId;
                        if (currentValue && currentValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            hiddenInput.value = currentValue;
                        } else if (initialDate) {
                            const year = initialDate.getFullYear();
                            const month = String(initialDate.getMonth() + 1).padStart(2, '0');
                            const day = String(initialDate.getDate()).padStart(2, '0');
                            hiddenInput.value = `${year}-${month}-${day}`;
                        }
                        input.parentElement.appendChild(hiddenInput);
                    }
                    
                    // Импортируем локаль (если доступна) или используем дефолтную
                    // Локаль может быть доступна через window или нужно импортировать
                    let localeEn = null;
                    if (typeof window.localeEn !== 'undefined') {
                        localeEn = window.localeEn;
                    }
                    
                    // Конфигурация календаря
                    const pickerConfig = {
                        dateFormat: function(date) {
                            return date.toLocaleString("en-US", {
                                year: "numeric",
                                day: "2-digit",
                                month: "2-digit",
                            });
                        },
                        selectedDates: initialDate ? [initialDate] : [],
                        maxDate: new Date(),
                        autoClose: true,
                        isMobile: false,
                        onRenderCell: function({ date, cellType }) {
                            const today = new Date();
                            today.setHours(23, 59, 59, 999);
                            const response = {
                                disabled: true,
                                classes: "disabled-class",
                                attrs: {
                                    title: "Cell is disabled",
                                },
                            };
                            
                            if (cellType === "day") {
                                const cellDate = new Date(date);
                                cellDate.setHours(0, 0, 0, 0);
                                if (cellDate > today) {
                                    return response;
                                }
                            }
                        },
                        onSelect: function({ date, formattedDate, datepicker }) {
                            const displayFormat = date.toLocaleString("en-US", {
                                year: "numeric",
                                day: "2-digit",
                                month: "2-digit",
                            });
                            
                            // Устанавливаем отображаемое значение
                            datepicker.$el.value = displayFormat;
                            
                            // Форматируем дату для скрытого поля
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            const hiddenValue = `${year}-${month}-${day}`;
                            
                            // Сохраняем ссылку на элемент для использования после закрытия календаря
                            const inputElement = datepicker.$el;
                            const parentElement = inputElement.parentElement;
                            
                            // Закрываем календарь сначала
                            datepicker.hide();
                            
                            // Обновляем DOM после того, как календарь закрылся
                            setTimeout(function() {
                                // Находим или создаем скрытое поле
                                let hiddenInput = parentElement.querySelector('input[name="birthday"][type="hidden"]');
                                if (!hiddenInput) {
                                    hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = 'birthday';
                                    hiddenInput.id = 'birthday-hidden-' + birthdayInputId;
                                    parentElement.appendChild(hiddenInput);
                                }
                                
                                // Устанавливаем значение скрытого поля
                                hiddenInput.value = hiddenValue;
                                
                                // Удаляем name из видимого input только если скрытое поле создано
                                if (hiddenInput && hiddenInput.parentElement) {
                                    inputElement.removeAttribute('name');
                                }
                                
                                // Триггерим событие input
                                const event = new Event('input', {
                                    cancelable: true,
                                    bubbles: true,
                                });
                                inputElement.dispatchEvent(event);
                            }, 100);
                        },
                    };
                    
                    // Добавляем локаль, если доступна
                    if (localeEn) {
                        pickerConfig.locale = localeEn;
                    }
                    
                    // Создаем календарь напрямую
                    birthdayPicker = new window.AirDatepicker('#' + birthdayInputId, pickerConfig);
                    
                    input.dataset.pickerInitialized = 'true';
                    input._birthdayPicker = birthdayPicker;
                    setupCalendarHandlers();
                    console.log('Birthday date picker initialized directly using AirDatepicker');
                } catch (error) {
                    console.error('Error initializing birthday date picker:', error);
                    if (initAttempts < maxAttempts) {
                        setTimeout(initBirthdayDatePicker, 100);
                    }
                }
            }
            
            function setupCalendarHandlers() {
                const input = document.querySelector('#' + birthdayInputId);
                if (!input || !birthdayPicker) return;
                
                function openCalendar(e) {
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    if (birthdayPicker) {
                        try {
                            if (typeof birthdayPicker.show === 'function') {
                                birthdayPicker.show();
                            } else if (typeof birthdayPicker.open === 'function') {
                                birthdayPicker.open();
                            }
                        } catch (err) {
                            console.error('Error opening calendar:', err);
                        }
                    }
                }
                
                const calendarIcon = document.getElementById('calendar-icon-' + birthdayInputId);
                if (calendarIcon) {
                    calendarIcon.style.pointerEvents = 'auto';
                    calendarIcon.style.cursor = 'pointer';
                    calendarIcon.addEventListener('click', openCalendar);
                }
                
                input.addEventListener('click', openCalendar);
                input.addEventListener('focus', openCalendar);
            }
            
            // Запускаем инициализацию
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(initBirthdayDatePicker, 200);
                });
            } else {
                setTimeout(initBirthdayDatePicker, 200);
            }
            
            // Скрываем все подсказки по умолчанию
            const allSidebarItems = document.querySelectorAll('.sidebar-item');
            allSidebarItems.forEach(item => {
                item.style.display = 'none';
            });

            // Функция для показа подсказки напротив соответствующего поля
            function showSidebarItem(fieldName) {
                // Скрываем все подсказки
                allSidebarItems.forEach(item => {
                    item.style.display = 'none';
                });
                
                // Находим поле слева
                const fieldElement = document.querySelector(`[data-field="${fieldName}"]`);
                if (!fieldElement) return;
                
                // Находим соответствующую подсказку справа
                const targetItem = document.querySelector(`.sidebar-item[data-field="${fieldName}"]`);
                if (!targetItem) return;
                
                // Получаем контейнеры
                const rowElement = document.querySelector('.verification-form__row');
                const sidebarWrapper = document.querySelector('.verification-form__sidebar-wrapper');
                
                if (!rowElement || !sidebarWrapper) return;
                
                // Получаем позиции относительно viewport
                const fieldRect = fieldElement.getBoundingClientRect();
                const rowRect = rowElement.getBoundingClientRect();
                const sidebarRect = sidebarWrapper.getBoundingClientRect();
                
                // Вычисляем позицию поля относительно контейнера row
                const fieldTopInRow = fieldRect.top - rowRect.top;
                
                // Позиционируем подсказку напротив поля
                targetItem.style.position = 'absolute';
                targetItem.style.top = Math.max(0, fieldTopInRow) + 'px';
                targetItem.style.display = 'block';
            }

            // Обновляем позицию при скролле
            let scrollTimeout;
            window.addEventListener('scroll', function() {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    const visibleItem = document.querySelector('.sidebar-item[style*="display: block"]');
                    if (visibleItem) {
                        const fieldName = visibleItem.getAttribute('data-field');
                        showSidebarItem(fieldName);
                    }
                }, 10);
            }, { passive: true });

            // Обработчики для полей формы
            const fieldMappings = {
                'full_name': 'full_name',
                'street': 'address',
                'street2': 'address',
                'city': 'address',
                'state': 'address',
                'zip': 'address',
                'country': 'address',
                'birthday': 'birthday',
                'phone': 'phone',
                'tax_id': 'tax_id'
            };

            // Добавляем обработчики для всех полей
            Object.keys(fieldMappings).forEach(fieldName => {
                const field = document.querySelector(`input[name="${fieldName}"], textarea[name="${fieldName}"]`);
                if (field) {
                    const sidebarField = fieldMappings[fieldName];
                    
                    // При фокусе
                    field.addEventListener('focus', function() {
                        showSidebarItem(sidebarField);
                    });
                    
                    // При клике
                    field.addEventListener('click', function() {
                        showSidebarItem(sidebarField);
                    });
                }
            });

            // Обработчик для всей секции Address
            const addressSection = document.querySelector('.address-section');
            if (addressSection) {
                addressSection.addEventListener('click', function(e) {
                    // Если клик не на самом input, показываем подсказку
                    if (!e.target.closest('input')) {
                        showSidebarItem('address');
                    }
                });
            }

            // Обработчики для клика на label и заголовки полей
            document.querySelectorAll('label, .label-name').forEach(label => {
                label.addEventListener('click', function(e) {
                    const formGroup = this.closest('[data-field]');
                    if (formGroup) {
                        const fieldName = formGroup.getAttribute('data-field');
                        if (fieldName) {
                            showSidebarItem(fieldName);
                        }
                    }
                });
            });

            // Обработчик для секции Social Media
            const socialSection = document.querySelector('[data-field="social"]');
            const socialMediaElements = document.querySelectorAll('.profile-social-wrap input[type="checkbox"], .profile-social-wrap label');
            
            if (socialSection) {
                socialSection.addEventListener('click', function(e) {
                    // Показываем подсказку при клике на любую часть секции
                    showSidebarItem('social');
                });
            }
            
            socialMediaElements.forEach(element => {
                element.addEventListener('click', function(e) {
                    showSidebarItem('social');
                });
            });

            // Обработчик для клика вне полей (скрываем все подсказки)
            document.addEventListener('click', function(e) {
                const isFormField = e.target.closest('input, textarea, select, label, .form-group');
                const isSocialSection = e.target.closest('.profile-social-wrap, [data-field="social"], h2');
                const isSidebar = e.target.closest('.verification-form__sidebar');
                const isButton = e.target.closest('button[type="submit"], .back');
                
                // Не скрываем подсказки при клике на поля формы, соцсети или сайдбар
                if (!isFormField && !isSocialSection && !isSidebar && !isButton) {
                    allSidebarItems.forEach(item => {
                        item.style.display = 'none';
                    });
                }
            });
        });
    </script>

    <style>
        .verification-form__row {
            display: flex;
            gap: 30px;
            align-items: flex-start;
            position: relative;
        }
        
        .verification-form__left-col {
            flex: 1;
        }
        
        .verification-form__sidebar-wrapper {
            flex: 0 0 350px;
            position: relative;
            min-height: 100px;
        }

        .sidebar-item {
            position: absolute;
            width: 100%;
            animation: fadeIn 0.3s ease-in;
            z-index: 10;
        }

        .sidebar-item-content {
            background-color: #F9F9F9;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group[data-field] {
            cursor: pointer;
        }

        .form-group[data-field] input,
        .form-group[data-field] textarea {
            cursor: text;
        }
        
        .social-item-wrapper {
            margin-bottom: 15px;
            width: 100%;
        }
        
        .social-url-input {
            margin-top: 10px;
            width: 100%;
        }
        
        .social-url-input input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        .social-url-input input:focus {
            outline: none;
            border-color: #FC7361;
        }
        
        .social-url-input input.error {
            border-color: #dc3545;
        }
        
        .profile-social-wrap {
            width: 100%;
        }
        
        /* Кастомные стили для AirDatepicker в красно-оранжевой палитре */
        .air-datepicker {
            --adp-color-primary: #FC7361 !important;
            --adp-color-primary-hover: #e86552 !important;
            --adp-color-current-date: #FC7361 !important;
            --adp-color-selected-date: #ffffff !important;
            --adp-color-selected-date-background: #FC7361 !important;
            --adp-color-day-name: #212529 !important;
            --adp-color-day-name-hover: #FC7361 !important;
            --adp-color-cell-hover: rgba(252, 115, 97, 0.15) !important;
            --adp-color-cell-selected: #ffffff !important;
            --adp-color-cell-selected-background: #FC7361 !important;
            --adp-color-cell-selected-hover: #e86552 !important;
            --adp-color-cell-disabled: #A4A0A0 !important;
            --adp-color-cell-disabled-background: #f5f5f5 !important;
            --adp-color-cell-other-month: #A4A0A0 !important;
            --adp-color-cell-other-month-hover: rgba(252, 115, 97, 0.1) !important;
            --adp-color-nav-arrow-hover: #FC7361 !important;
            --adp-color-nav-action-hover: #FC7361 !important;
            --adp-color-nav-action-active: #FC7361 !important;
            --adp-color-button-hover: rgba(252, 115, 97, 0.1) !important;
            --adp-color-button-active: #FC7361 !important;
            --adp-color-button-active-text: #ffffff !important;
            --adp-color-button-text: #212529 !important;
            --adp-color-button-text-hover: #FC7361 !important;
            --adp-border-radius: 8px;
            --adp-font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            --adp-font-size: 14px;
            --adp-width: 300px;
            --adp-z-index: 100;
            box-shadow: 0 4px 16px rgba(252, 115, 97, 0.2) !important;
            border: 1px solid rgba(252, 115, 97, 0.3) !important;
            background-color: #ffffff;
        }

        .air-datepicker--pointer {
            --adp-pointer-color: #FC7361 !important;
        }
        
        .air-datepicker--pointer::before {
            border-bottom-color: #FC7361 !important;
        }

        .air-datepicker-nav {
            border-bottom: 1px solid rgba(252, 115, 97, 0.15) !important;
            padding: 12px 16px;
            background-color: #ffffff;
        }

        .air-datepicker-nav--title {
            color: #212529 !important;
            font-weight: 600;
        }

        .air-datepicker-nav--title:hover {
            color: #FC7361 !important;
        }

        .air-datepicker-nav--action {
            color: #212529 !important;
            transition: all 0.2s ease;
        }

        .air-datepicker-nav--action:hover {
            color: #FC7361 !important;
            background-color: rgba(252, 115, 97, 0.1) !important;
        }

        .air-datepicker-nav--action svg {
            fill: currentColor;
        }

        /* Стили для названий дней недели */
        .air-datepicker--day-name {
            color: #212529 !important;
            font-weight: 600;
            font-size: 12px;
        }
        
        /* Выходные дни (суббота и воскресенье) - оранжевый цвет */
        .air-datepicker--day-name:nth-child(6),
        .air-datepicker--day-name:nth-child(7) {
            color: #FC7361 !important;
            font-weight: 700;
        }

        /* Ячейки календаря */
        .air-datepicker-cell {
            color: #212529 !important;
            transition: all 0.2s ease;
        }

        .air-datepicker-cell:hover {
            background-color: rgba(252, 115, 97, 0.15) !important;
            color: #FC7361 !important;
        }

        /* Выбранная дата */
        .air-datepicker-cell.-selected- {
            background-color: #FC7361 !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 4px;
        }

        .air-datepicker-cell.-selected-:hover {
            background-color: #e86552 !important;
            color: #ffffff !important;
        }

        /* Текущая дата (сегодня) */
        .air-datepicker-cell.-current- {
            color: #FC7361 !important;
            font-weight: 600;
        }

        .air-datepicker-cell.-current-.-selected- {
            background-color: #FC7361 !important;
            color: #ffffff !important;
        }

        /* Отключенные даты */
        .air-datepicker-cell.-disabled- {
            color: #A4A0A0 !important;
            background-color: #f5f5f5 !important;
            cursor: not-allowed;
            opacity: 0.5;
        }

        /* Даты из других месяцев */
        .air-datepicker-cell.-other-month- {
            color: #A4A0A0 !important;
            opacity: 0.4;
        }

        .air-datepicker-cell.-other-month-:hover {
            background-color: rgba(252, 115, 97, 0.05) !important;
            color: #FC7361 !important;
        }

        /* Кнопки внизу календаря */
        .air-datepicker--buttons {
            border-top: 1px solid rgba(252, 115, 97, 0.15) !important;
            padding: 8px;
            background-color: #ffffff;
        }

        .air-datepicker--button {
            color: #212529 !important;
            transition: all 0.2s ease;
            border-radius: 4px;
            padding: 6px 12px;
        }

        .air-datepicker--button:hover {
            background-color: rgba(252, 115, 97, 0.1) !important;
            color: #FC7361 !important;
        }

        .air-datepicker--button.-active- {
            background-color: #FC7361 !important;
            color: #ffffff !important;
        }

        .air-datepicker--button.-active-:hover {
            background-color: #e86552 !important;
            color: #ffffff !important;
        }
        
        /* Переопределение стандартных цветов AirDatepicker */
        .air-datepicker-cell.-selected-,
        .air-datepicker-cell.-selected-.-current- {
            background: #FC7361 !important;
            color: #ffffff !important;
        }
        
        .air-datepicker-cell.-range-from-,
        .air-datepicker-cell.-range-to- {
            background: #FC7361 !important;
            color: #ffffff !important;
        }
        
        .air-datepicker-cell.-in-range- {
            background: rgba(252, 115, 97, 0.1) !important;
        }
        
        @media (max-width: 992px) {
            .verification-form__row {
                flex-direction: column;
            }
            .verification-form__sidebar-wrapper {
                position: static !important;
                margin-top: 30px;
                flex: 1 1 100%;
            }
            .sidebar-item {
                position: static !important;
            }
        }
    </style>
@endsection
