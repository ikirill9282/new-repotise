@extends('layouts.site')

@push('head')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.min.css">
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
@endpush

@section('content')
  <div class="the-content">
    <section class="verification-form__wrap">
        <div class="container">
            <div class="col">
                @include('site.components.breadcrumbs')
                <div class="verification-form__title">
                    <h1>
                        Account Verification
                    </h1>
                    <p>
                        Please provide the following information to verify your account and enable payouts. This
                        process helps us ensure the security of your account and comply with tax regulations.
                        Your
                        information is securely encrypted.
                    </p>
                </div>
                <div class="verification-form__form">
                    <form action="#">
                        <h2>
                            Personal Details
                        </h2>
                        <label class="req">
                            <span class="label-name">
                                Full Name
                            </span>
                            <input type="text" name="user-name" placeholder="Your legal first and last name">
                            <i>
                                <svg width="13.332031" height="16.000488" viewBox="0 0 13.332 16.0005"
                                    fill="none" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <desc>
                                        Created with Pixso.
                                    </desc>
                                    <defs />
                                    <path id="Vector"
                                        d="M6.66 10C6.48 10 6.32 9.93 6.19 9.8C6.07 9.68 6 9.51 6 9.33C6.02 8.89 6.15 8.47 6.38 8.1C6.61 7.73 6.92 7.42 7.3 7.2C7.55 7.06 7.75 6.85 7.87 6.6C7.99 6.34 8.02 6.06 7.97 5.78C7.92 5.52 7.79 5.28 7.6 5.09C7.41 4.9 7.17 4.77 6.91 4.72C6.71 4.68 6.51 4.69 6.32 4.74C6.14 4.79 5.96 4.88 5.81 5C5.66 5.13 5.54 5.29 5.45 5.46C5.37 5.64 5.33 5.83 5.33 6.03C5.33 6.21 5.26 6.38 5.13 6.5C5.01 6.63 4.84 6.7 4.66 6.7C4.48 6.7 4.32 6.63 4.19 6.5C4.07 6.38 4 6.21 4 6.03C4 5.64 4.08 5.26 4.25 4.9C4.41 4.55 4.65 4.24 4.95 3.99C5.23 3.75 5.57 3.57 5.92 3.47C6.28 3.37 6.66 3.34 7.02 3.39C7.39 3.44 7.75 3.57 8.06 3.76C8.38 3.96 8.65 4.22 8.86 4.53C9.07 4.83 9.21 5.18 9.28 5.55C9.35 5.91 9.34 6.29 9.25 6.65C9.17 7.01 9.01 7.35 8.78 7.65C8.56 7.94 8.27 8.19 7.95 8.37C7.78 8.47 7.64 8.61 7.53 8.78C7.42 8.94 7.35 9.13 7.33 9.33C7.33 9.51 7.26 9.68 7.13 9.8C7.01 9.93 6.84 10 6.66 10ZM7.19 15.84C8.63 15.26 13.33 12.98 13.33 8.02L13.33 4.58C13.33 3.88 13.11 3.19 12.7 2.63C12.29 2.06 11.71 1.63 11.05 1.41L6.87 0.03C6.74 -0.02 6.59 -0.02 6.45 0.03L2.28 1.41C1.61 1.63 1.03 2.06 0.62 2.63C0.21 3.19 0 3.88 0 4.58L0 8.02C0 12.4 4.67 15.07 6.1 15.79C6.27 15.9 6.46 15.96 6.66 16C6.85 15.98 7.03 15.92 7.19 15.84ZM10.62 2.68C11.02 2.81 11.37 3.07 11.62 3.41C11.86 3.75 12 4.16 12 4.58L12 8.02C12 12.15 7.94 14.1 6.69 14.6C5.43 13.97 1.33 11.63 1.33 8.02L1.33 4.58C1.33 4.16 1.46 3.75 1.71 3.41C1.95 3.07 2.3 2.81 2.7 2.68L6.66 1.36L10.62 2.68ZM6.66 11.33C6.53 11.33 6.4 11.37 6.29 11.44C6.18 11.51 6.1 11.62 6.05 11.74C6 11.86 5.98 12 6.01 12.13C6.03 12.25 6.1 12.37 6.19 12.47C6.28 12.56 6.4 12.62 6.53 12.65C6.66 12.68 6.8 12.66 6.92 12.61C7.04 12.56 7.14 12.48 7.22 12.37C7.29 12.26 7.33 12.13 7.33 12C7.33 11.82 7.26 11.65 7.13 11.52C7.01 11.4 6.84 11.33 6.66 11.33Z"
                                        fill="#A4A0A0" fill-opacity="1.000000" fill-rule="nonzero" />
                                </svg>
                            </i>
                        </label>
                        <span class="label-name custom-label">
                            Address
                        </span>
                        <label class="half req">
                            <input placeholder="Street address" type="text" name="user-street">
                        </label>
                        <label class="half">
                            <input placeholder="Street address line 2" type="text" name="user-street-2">
                        </label>
                        <label class="half req">
                            <input placeholder="city" type="text" name="user-city">
                        </label>
                        <label class="half req">
                            <input placeholder="State/Province" type="text" name="user-state">
                        </label>
                        <label class="half req">
                            <input placeholder="Zip code" type="text" name="user-zip">
                        </label>
                        <label class="half req">
                            <input placeholder="USA" type="text" name="user-country">
                        </label>
                        <label class="half req">
                            <span class="label-name">
                                Date of Birth
                            </span>
                            <input type="date" placeholder="MM/DD/YYYY">
                        </label>
                        <label>
                            <span class="label-name">
                                Tax ID / Identification Number
                            </span>
                            <input type="text" placeholder="Tax ID / Identification Number">
                        </label>
                        <label>
                            <span class="label-name">
                                Phone Number
                            </span>
                            <input type="tel" id="phone" name="user-phone" placeholder="+1">
                        </label>

                        <h2>
                            Social Media Verification (Reccommended)
                        </h2>
                        <p>
                            To expedite and strengthen your account verification, we highly recommend linking
                            your
                            social media profiles.
                        </p>
                        <div class="profile-social-wrap">
                            <label class="check-label">
                                <div class="checkbox">
                                    <div class="item">
                                        <img src="{{ asset('assets/img/icons/youtube.svg') }}" alt="">
                                        <span>
                                            Youtube channel
                                        </span>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="youtube">
                                        <span class="decor"></span>
                                    </div>
                                </div>
                            </label>
                            <label class="check-label">
                                <div class="checkbox">
                                    <div class="item">
                                        <img src="{{ asset('assets/img/icons/tiktok.svg') }}" alt="">
                                        <span>
                                            TikTok Account
                                        </span>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="tiktok">
                                        <span class="decor"></span>
                                    </div>
                                </div>
                            </label>
                            <label class="check-label">
                                <div class="checkbox">
                                    <div class="item">
                                        <img src="{{ asset('assets/img/icons/google.svg') }}" alt="">
                                        <span>
                                            Google Account
                                        </span>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="google">
                                        <span class="decor"></span>
                                    </div>
                                </div>
                            </label>
                            <label class="check-label">
                                <div class="checkbox">
                                    <div class="item">
                                        <img src="{{ asset('assets/img/icons/facebook.svg') }}" alt="">
                                        <span>
                                            Facebook Account/Page
                                        </span>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="facebook">
                                        <span class="decor"></span>
                                    </div>
                                </div>
                            </label>
                            <label class="check-label">
                                <div class="checkbox">
                                    <div class="item">
                                        <img src="{{ asset('assets/img/icons/insta.svg') }}" alt="">
                                        <span>
                                            Instagram Account
                                        </span>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="insta">
                                        <span class="decor"></span>
                                    </div>
                                </div>
                            </label>
                            <label class="check-label">
                                <div class="checkbox">
                                    <div class="item">
                                        <img src="{{ asset('assets/img/icons/xai.svg') }}" alt="">
                                        <span>
                                            X (Twitter) Account
                                        </span>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="xai">
                                        <span class="decor"></span>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <p>
                            Don't see your social network listed? <a href="#">Contact us</a> to verify other
                            social
                            media profiles manually.
                        </p>

                        <div class="action">
                            <button class="back">
                                Back
                            </button>
                            <button type="submit">
                                Submit for Verification
                            </button>
                        </div>
                        <div class="info">
                            <span>
                                By clicking 'Submit for Verification', you certify that the information provided
                                is
                                accurate and truthful. This data will be used for account verification, tax form
                                generation (e.g., 1099 forms), and enabling payouts via Stripe. You provide your
                                digital signature for these purposes and agree to our Terms of Service, <a
                                    href="#">Privacy
                                    Policy</a>, and <a href="#">Other Terms</a>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col">

            </div>
        </div>
    </section>
</div>
@endsection