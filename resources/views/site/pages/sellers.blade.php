@extends('layouts.site')

@section('content')
    <div class="the-content bg-light">
        {{-- HERI --}}
        <section class="invite-hero hero relative">
            @include('site.components.parallax', ['class' => 'parallax-invite'])
            <div class="container">
                <div x-data="{}" class="invite-hero__text hero__text">
                    <h1>Turn Your Travel Passion into Profit</h1>
                    <p>
                        Join TrekGuider to monetize your travel expertise and reach a
                        global audience. It's simple, profitable, and built for creators
                        like you.
                    </p>
                    @if (!auth()->check())
                        <x-btn x-on:click.prevent="Livewire.dispatch('modal.openReg')">
                            Get Started
                        </x-btn>
                    @else
                        <x-btn href="{{ route('verify') }}">
                            Get Started
                        </x-btn>
                    @endif
                </div>
            </div>
        </section>

        {{-- CALCULATOR --}}
        @livewire('invite-calculator')

        {{-- SETPS --}}
        <section class="invite-launch">
            <div class="container">
                <div class="invite-launch__title">
                    <h2 class="section-title">
                        Estimate Only. Your actual income will depend on your sales.
                    </h2>
                </div>
                <div class="invite-launch__items !grid !grid-cols-1 lg:!grid-cols-3 gap-3 !m-0">
                    <div class="item !m-0 !w-full">
                        <div class="num">
                            <span>1</span>
                        </div>
                        <div class="text">
                            <h3>Sign Up & Verify</h3>
                            <p>
                                Create your seller account in minutes and get instantly
                                verified.
                            </p>
                            <x-link class="{{ auth()->check() ? '' : 'open_auth' }}"
                                href="{{ auth()->check() ? route('profile') : '#' }}">
                                Sign Up
                            </x-link>
                        </div>
                    </div>
                    <div class="item !m-0 !w-full">
                        <div class="num">
                            <span>2</span>
                        </div>
                        <div class="text">
                            <h3>Upload Products</h3>
                            <p>
                                Upload your content, set your prices, discounts, and
                                customize your listings.
                            </p>
                            <x-link class="{{ auth()->check() ? '' : 'open_auth' }}"
                                href="{{ auth()->check() ? route('profile') : '#' }}"> Upload Products </x-link>
                        </div>
                    </div>
                    <div class="item !m-0 !w-full">
                        <div class="num">
                            <span>3</span>
                        </div>
                        <div class="text">
                            <h3>Launch & Grow</h3>
                            <p>
                                Go live, attract customers, generate income, and build your
                                brand with your Creator Page.
                            </p>

                            <x-link class="{{ auth()->check() ? '' : 'open_auth' }}"
                                href="{{ auth()->check() ? route('profile') : '#' }}"> Start Earning </x-link>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- ADVANTAGES --}}
        <section class="invite-power">
            <div class="container">
                <div class="invite__title">
                    <h2 class="section-title">
                        Powerful Tools to Grow Your Travel Business on TrekGuider
                    </h2>
                </div>
                <div class="invite-swiper">
                    <div class="invite-power__items">
                        <div class="item">
                            <div class="img">
                                <img src="{{ asset('assets/img/pi1.png') }}" alt="" />
                            </div>
                            <div class="text">
                                <h3>Personal Landing Page</h3>
                                <p>
                                    Your all-in-one professional landing page. Showcase your
                                    brand, products, and social media, attracting customers and
                                    partnership inquiries
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <div class="img">
                                <img src="{{ asset('assets/img/pi2.png') }}" alt="" />
                            </div>
                            <div class="text">
                                <h3>Unlimited Content Formats</h3>
                                <p>
                                    Monetize any digital travel content you create: guides,
                                    maps, videos, courses, templates, and more. Offer both
                                    one-time purchases and subscriptions.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <div class="img">
                                <img src="{{ asset('assets/img/pi3.png') }}" alt="" />
                            </div>
                            <div class="text">
                                <h3>Boost Sales with Articles</h3>
                                <p>
                                    Publish articles and helpful content to promote your
                                    products, engage your audience, and increase your reach.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <div class="img">
                                <img src="{{ asset('assets/img/pi4.png') }}" alt="" />
                            </div>
                            <div class="text">
                                <h3>Direct Fan Support</h3>
                                <p>
                                    Let your fans support you directly with one-time tips or
                                    recurring subscriptions. Get rewarded for your creativity.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <div class="img">
                                <img src="{{ asset('assets/img/pi5.png') }}" alt="" />
                            </div>
                            <div class="text">
                                <h3>Worldwide Payments</h3>
                                <p>
                                    Seamlessly accept payments from global buyers in any
                                    currency and payment method, powered by Stripe. Expand your
                                    market and boost sales.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <div class="img">
                                <img src="{{ asset('assets/img/pi6.png') }}" alt="" />
                            </div>
                            <div class="text">
                                <h3>Global Marketplace</h3>
                                <p>
                                    Sign up and sell from any country, reach a global market no
                                    matter where you are. TrekGuider is open to creators
                                    worldwide.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <div class="img">
                                <img src="{{ asset('assets/img/pi7.png') }}" alt="" />
                            </div>
                            <div class="text">
                                <h3>Trusted & Secure</h3>
                                <p>
                                    Benefit from a secure, US-based platform with robust
                                    copyright protection, automated tax forms, and legal
                                    compliance. Sell with confidence.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <div class="img">
                                <img src="{{ asset('assets/img/pi8.png') }}" alt="" />
                            </div>
                            <div class="text">
                                <h3>Data-Driven Growth</h3>
                                <p>
                                    Track key performance indicators in real-time. Get full
                                    sales transparency to analyze and optimize your income
                                    strategy.
                                </p>
                            </div>
                        </div>
                        <div class="item">
                            <div class="img">
                                <img src="{{ asset('assets/img/pi9.png') }}" alt="" />
                            </div>
                            <div class="text">
                                <h3>Built-in Gift Feature</h3>
                                <p>
                                    Boost your sales potential! Buyers can easily purchase your
                                    non-subscription products as gifts for friends and family,
                                    creating new revenue opportunities for you.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- LEVELS --}}
        <section class="invite-honest" id="levels">
            <div class="container">
                <div class="invite-honest__title">
                    <h2 class="section-title">Honest & Transparent Partnership</h2>
                </div>
                <div class="invite-honest__items">
                    <div class="item">
                        <div class="title">

                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.92021 0.98092L10.4431 4.02321C10.5923 4.32188 10.8804 4.52898 11.2145 4.5769L14.6211 5.06726C15.4628 5.18878 15.7977 6.20887 15.1886 6.79337L12.7252 9.16045C12.4831 9.39322 12.3729 9.72783 12.4301 10.0564L13.0115 13.3983C13.1547 14.2249 12.2748 14.8557 11.5225 14.4646L8.47768 12.8857C8.17918 12.7308 7.82168 12.7308 7.52232 12.8857L4.4775 14.4646C3.72519 14.8557 2.84533 14.2249 2.98937 13.3983L3.56987 10.0564C3.62714 9.72783 3.51694 9.39322 3.27485 9.16045L0.8114 6.79337C0.202263 6.20887 0.537201 5.18878 1.37889 5.06726L4.78554 4.5769C5.11961 4.52898 5.40856 4.32188 5.55781 4.02321L7.07979 0.98092C7.45638 0.228691 8.54362 0.228691 8.92021 0.98092Z"
                                    fill="#FFDB0C" stroke="#FFDB0C" stroke-width="0.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <h2 class="!text-2xl">Level 1: Beginner</h2>
                        </div>
                        <div class="text">
                            <p>Commission: 10%</p>
                            <ul>
                                <li>Sales up to $100</li>
                                <li>Free Storage: 300MB</li>
                            </ul>
                        </div>
                    </div>
                    <div class="item">
                        <div class="title">

                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_5009_101696)">
                                    <path
                                        d="M18.7512 3.81348H16.1795C16.0147 3.81351 15.8536 3.86241 15.7166 3.95398C15.5796 4.04555 15.4728 4.17568 15.4098 4.32793C15.3467 4.48018 15.3302 4.64771 15.3623 4.80934C15.3945 4.97096 15.4738 5.11943 15.5903 5.23598L16.4953 6.14098L13.507 9.12848C13.3482 9.28022 13.137 9.36491 12.9174 9.36491C12.6978 9.36491 12.4866 9.28022 12.3278 9.12848L12.1862 8.98598C11.71 8.53104 11.0768 8.27716 10.4182 8.27716C9.75968 8.27716 9.12648 8.53104 8.65032 8.98598L4.40032 13.236C4.24373 13.393 4.15594 13.6058 4.15625 13.8276C4.15656 14.0493 4.24496 14.2619 4.40199 14.4185C4.55902 14.5751 4.77182 14.6629 4.99358 14.6625C5.21534 14.6622 5.4279 14.5738 5.58449 14.4168L9.83449 10.1668C9.99317 10.0149 10.2044 9.93006 10.4241 9.93006C10.6438 9.93006 10.855 10.0149 11.0137 10.1668L11.1553 10.3093C11.6317 10.7639 12.2648 11.0175 12.9232 11.0175C13.5817 11.0175 14.2148 10.7639 14.6912 10.3093L17.6795 7.32098L18.5845 8.22598C18.7014 8.3406 18.8495 8.41823 19.0103 8.44918C19.1711 8.48013 19.3375 8.46302 19.4886 8.39999C19.6397 8.33696 19.7689 8.23081 19.8601 8.09478C19.9513 7.95875 20.0003 7.79889 20.0012 7.63514V5.06348C20.0012 4.73196 19.8695 4.41401 19.635 4.17959C19.4006 3.94517 19.0827 3.81348 18.7512 3.81348Z"
                                        fill="#158CE8" />
                                    <path
                                        d="M19.1667 18.8133H4.16667C3.50363 18.8133 2.86774 18.5499 2.3989 18.0811C1.93006 17.6123 1.66667 16.9764 1.66667 16.3133V1.33333C1.66667 1.11232 1.57887 0.900358 1.42259 0.744078C1.26631 0.587797 1.05435 0.5 0.833333 0.5C0.61232 0.5 0.400358 0.587797 0.244078 0.744078C0.0877974 0.900358 0 1.11232 0 1.33333L0 16.3133C0.00132321 17.418 0.440735 18.477 1.22185 19.2582C2.00296 20.0393 3.062 20.4787 4.16667 20.48H19.1667C19.3877 20.48 19.5996 20.3922 19.7559 20.2359C19.9122 20.0796 20 19.8677 20 19.6467C20 19.4257 19.9122 19.2137 19.7559 19.0574C19.5996 18.9011 19.3877 18.8133 19.1667 18.8133Z"
                                        fill="#158CE8" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_5009_101696">
                                        <rect width="20" height="20" fill="white"
                                            transform="translate(0 0.5)" />
                                    </clipPath>
                                </defs>
                            </svg>

                            <h2 class="!text-2xl">Level 2: Growth</h2>
                        </div>
                        <div class="text">
                            <p>Commission: 8%</p>
                            <ul>
                                <li>Sales up to $300</li>
                                <li>Free Storage: 500MB</li>
                            </ul>
                        </div>
                    </div>
                    <div class="item">
                        <div class="title">
                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.3651 2.81501C10.2446 2.77693 10.1142 2.78586 10.0001 2.84001C9.94371 2.8664 9.89369 2.90463 9.85343 2.95208C9.81317 2.99953 9.7836 3.0551 9.76673 3.11501L9.37007 4.64334C9.23133 5.17742 9.02993 5.69323 8.77007 6.18001C8.3659 6.93584 7.75507 7.51667 7.1884 8.00501L5.99007 9.03834C5.8985 9.1175 5.82698 9.21721 5.78136 9.32932C5.73573 9.44143 5.7173 9.56274 5.72757 9.68334L6.40423 17.5108C6.42069 17.701 6.50776 17.878 6.64828 18.0071C6.7888 18.1362 6.97257 18.208 7.1634 18.2083H11.0376C13.6509 18.2083 15.8534 16.39 16.2759 13.9475L16.8634 10.5475C16.8825 10.4379 16.8773 10.3255 16.8484 10.2181C16.8195 10.1107 16.7675 10.011 16.6959 9.92581C16.6244 9.84064 16.5352 9.77212 16.4344 9.72506C16.3336 9.67799 16.2238 9.65352 16.1126 9.65334H11.7951C10.9809 9.65334 10.3617 8.92334 10.4934 8.12001L11.0459 4.75001C11.1223 4.28846 11.1008 3.816 10.9826 3.36334C10.9491 3.24365 10.886 3.1343 10.7993 3.04533C10.7125 2.95637 10.6047 2.89063 10.4859 2.85418L10.3651 2.81501ZM9.45673 1.71418C9.85982 1.52063 10.3217 1.48873 10.7476 1.62501L10.8684 1.66418C11.5159 1.87251 12.0209 2.38918 12.1926 3.04918C12.3534 3.67084 12.3834 4.31918 12.2792 4.95251L11.7267 8.32251C11.725 8.33245 11.7255 8.34264 11.7282 8.35237C11.7308 8.3621 11.7356 8.37113 11.7421 8.37883C11.7486 8.38653 11.7567 8.39271 11.7659 8.39695C11.775 8.40118 11.785 8.40337 11.7951 8.40334H16.1117C17.3617 8.40334 18.3084 9.53001 18.0951 10.7608L17.5076 14.1608C16.9759 17.2342 14.2259 19.4583 11.0376 19.4583H7.1634C6.65973 19.4579 6.17451 19.2687 5.80349 18.9281C5.43247 18.5875 5.20259 18.1201 5.15923 17.6183L4.48173 9.79084C4.45432 9.47297 4.50273 9.15313 4.62297 8.8576C4.74321 8.56207 4.93185 8.29929 5.1734 8.09084L6.3734 7.05751C6.91923 6.58751 7.3784 6.13168 7.66673 5.59084C7.88104 5.19153 8.04666 4.76795 8.16007 4.32918L8.55673 2.80168C8.61856 2.56583 8.73113 2.34632 8.88658 2.15849C9.04203 1.97065 9.23661 1.81902 9.45673 1.71418ZM2.4734 8.40418C2.63445 8.39713 2.79199 8.45259 2.91312 8.55897C3.03424 8.66534 3.10958 8.8144 3.1234 8.97501L3.93173 18.3383C3.94542 18.4778 3.93055 18.6185 3.88802 18.752C3.84548 18.8855 3.77618 19.0089 3.68435 19.1148C3.59252 19.2206 3.48008 19.3066 3.3539 19.3674C3.22772 19.4283 3.09046 19.4629 2.95048 19.469C2.81051 19.475 2.67077 19.4525 2.53979 19.4027C2.40882 19.353 2.28936 19.2771 2.18871 19.1796C2.08807 19.0821 2.00835 18.9652 1.95442 18.8359C1.90049 18.7066 1.87349 18.5676 1.87507 18.4275V9.02834C1.87492 8.8671 1.93709 8.71204 2.0486 8.59557C2.1601 8.47909 2.3123 8.41106 2.4734 8.40418Z"
                                    fill="#FC7361" />
                            </svg>

                            <h2 class="!text-2xl">Level 3: Pro</h2>
                            <span class="subtitle !top-0 translate-y-[-100%]">New Seller Bonus for 30 Days!</span>
                        </div>
                        <div class="text">
                            <p>Commission: 5%</p>
                            <ul>
                                <li>Sales: $300+</li>
                                <li>Free Storage: 1GB</li>
                            </ul>
                        </div>
                    </div>
                    <div class="item">
                        <div class="title">

                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_5009_71059)">
                                    <path
                                        d="M19.3507 5.28583L16.8416 1.89583C16.533 1.46445 16.1261 1.11281 15.6545 0.870064C15.1829 0.62732 14.6603 0.500461 14.1299 0.500001H5.87158C5.34378 0.499506 4.8234 0.624354 4.35327 0.864271C3.88315 1.10419 3.47671 1.45231 3.16742 1.88L0.613251 5.29417C0.197388 5.88058 -0.0173775 6.5859 0.00109982 7.30456C0.0195771 8.02322 0.270303 8.71657 0.715751 9.28083L8.04242 19.5542C8.27591 19.8498 8.5735 20.0886 8.91274 20.2524C9.25199 20.4163 9.62401 20.5009 10.0008 20.5C10.384 20.4992 10.7619 20.4106 11.1054 20.2408C11.449 20.0711 11.749 19.8247 11.9824 19.5208L19.2508 9.36167C19.7161 8.78916 19.9784 8.07841 19.9965 7.34082C20.0146 6.60323 19.7875 5.88048 19.3507 5.28583ZM15.4941 2.87583L18.0091 6.27417C18.0224 6.2925 18.0258 6.31417 18.0391 6.33333H13.9624L12.7724 2.16667H14.1299C14.3974 2.1675 14.6608 2.2323 14.8981 2.35567C15.1354 2.47904 15.3397 2.65739 15.4941 2.87583ZM10.0008 16.4317L7.75075 8H12.2508L10.0008 16.4317ZM7.77242 6.33333L8.96242 2.16667H11.0391L12.2291 6.33333H7.77242ZM4.51492 2.86417C4.66954 2.64866 4.87321 2.47302 5.1091 2.35174C5.34499 2.23046 5.60634 2.16702 5.87158 2.16667H7.22908L6.03908 6.33333H1.93242C1.94408 6.315 1.94742 6.2925 1.96075 6.275L4.51492 2.86417ZM2.04075 8.27083C1.98058 8.18585 1.92894 8.09513 1.88658 8H6.02742L8.52242 17.3583L2.04075 8.27083ZM11.4766 17.3667L13.9766 8H18.1316C18.0779 8.12439 18.0099 8.24212 17.9291 8.35083L11.4766 17.3667Z"
                                        fill="#FF2C0C" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_5009_71059">
                                        <rect width="20" height="20" fill="white"
                                            transform="translate(0 0.5)" />
                                    </clipPath>
                                </defs>
                            </svg>

                            <h2 class="!text-2xl">Level 4: Exclusive</h2>
                        </div>
                        <div class="text">
                            <p>Contact Us for Exclusive Commission</p>
                            <ul>
                                <li>Best Rates for Top Sellers</li>
                                <li>Unlimited</li>
                            </ul>
                            <x-link href="{{ route('help-center') }}">Contact us</x-link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- NOTIFICATION --}}
        <section class="breaker">
            <div class="container">
                <x-card class="flex justify-center items-center border !rounded-3xl">
                    <div class="max-w-xl">
                        <p>
                            Please note that Stripe payment processing fees are separate
                            and payout fees also apply. Once you reach a commission level,
                            it's locked in permanently.
                        </p>
                    </div>
                    <div class="action">
                        <x-btn data-goto href="#faq" class="hover:!text-light hover:!border-second" outlined>
                            Detailed Fee Information in FAQ.
                        </x-btn>
                    </div>
                </x-card>
            </div>
        </section>

        {{-- FAQ --}}
        <section class="!py-12.5" id="faq">
            <div class="container">
                <x-card class="lg:!p-12 border">
                    <h2 class="section-title !text-2xl md:!text-3xl !mb-8 md:!mb-18">Frequently Asked Questions</h2>

                    @php
$faqGettingStarted = [
    [
        'title' => 'Who can become a seller on TrekGuider?',
        'text' => 'Anyone who creates travel content and is 18 years of age or older can become a seller on TrekGuider, regardless of citizenship or country of residence. We welcome both U.S. residents and non-U.S. residents, as well as creators from most countries worldwide. The only exceptions are regions currently under international sanctions. For detailed registration requirements and a list of excluded regions, please refer to our Terms and Conditions.',
    ],
    [
        'title' => 'How do I get verified on TrekGuider?',
        'text' => 'Anyone who creates travel content and is 18 years of age or older can become a seller on TrekGuider, regardless of citizenship or country of residence. We welcome both U.S. residents and non-U.S. residents, as well as creators from most countries worldwide. The only exceptions are regions currently under international sanctions. For detailed registration requirements and a list of excluded regions, please refer to our Terms and Conditions.',
    ],
];

$faqSelling = [
    [
        'title' => 'What kind of content can I sell on TrekGuider?',
        'text' => 'On TrekGuider, you can sell any type of digital product for travelers. This includes travel guides, custom itineraries, interactive maps, audio guides, video tutorials, photo presets, travel templates, checklists, eBooks, animations, spreadsheets, and much more. Offer your content as one-time purchases or create subscription options for exclusive content.',
    ],
    [
        'title' => 'How does TrekGuider protect my content and copyrights?',
        'text' => 'TrekGuider takes the protection of your copyrights and content very seriously. As a platform registered in the U.S. jurisdiction, we adhere to strict U.S. copyright and intellectual property laws. We also implement advanced technical security measures to safeguard your content from unauthorized access and copying. You can be confident that your copyrights are reliably protected on TrekGuider.',
    ],
    [
        'title' => 'How do refunds work on TrekGuider, and how can I manage them?',
        'text' => 'TrekGuider gives you complete control over the refund policy for your products. In your account settings, you can select from pre-set refund policy options (e.g., 7 days, 30 days, 160 days, no refunds). You can manage refund requests yourself and easily issue refunds to buyers through your seller dashboard. Our flexible refund system allows you to set terms that best suit your business and customer needs.',
    ],
];

$faqCreatorPage = [
    [
        'title' => 'What is a TrekGuider Creator Page and why do I need one?',
        'text' => "Your TrekGuider Creator Page is your own personal landing page, specifically designed to promote your brand and content. It's your online presence where you can share your story, highlight your expertise, showcase your product portfolio, link to your social media, and provide contact information for collaborations. You don't need a separate website – your TrekGuider Creator Page provides everything you need to succeed!",
    ],
    [
        'title' => 'How do I accept tips (donations) from fans on my Creator Page?',
        'text' => 'The tips (donations) feature allows your fans to support you directly and show their appreciation for your content. Any visitor to your Creator Page can send you a one-time tip or set up a recurring subscription for ongoing support. Tips received are credited to your platform balance, and TrekGuider charges a low 5% commission on tips.',
    ],
];

$faqEarnings = [
    [
        'title' => 'How much can I earn on TrekGuider?',
        'text' => 'Your earning potential on TrekGuider is unlimited! Your income depends entirely on you and the quality of your content. There are no caps on how much you can earn. For example, with average monthly sales of $2,000, your annual income (after platform and payment processing fees) could be over $22,000. To maximize your earnings, take advantage of all TrekGuider offers: sell more products, offer subscriptions, accept tips, publish articles to attract a wider audience, and in the future, offer travel services and tours.',
    ],
    [
        'title' => 'In which countries can I sell and accept payments on TrekGuider?',
        'text' => "You can sell your products to travelers worldwide! TrekGuider supports international sales in virtually every country where Stripe is available. Buyers can pay for your products in any currency and using local payment methods supported by Stripe. All your earnings are automatically converted to U.S. Dollars and credited to your balance. For more details on Stripe's supported payment methods, please see Stripe's Payment Methods page.",
    ],
    [
        'title' => 'How quickly and easily can I withdraw my earnings from TrekGuider?',
        'text' => 'TrekGuider offers fast and convenient payout options:
						Standard Payout: to your local bank account (available in most countries). Processing Time: 1-3 business days. Stripe Fee: 0.25% + $0.25.
						Instant Payout: to your debit card (available in supported countries). Processing Time: Within 20 minutes, even on weekends and holidays. Stripe Fee: 1%.
						Other Options: Bank transfer, PayPal, and local payment methods (depending on your country). Processing times and fees may vary.
						Minimum Withdrawal Amount: $40.
						First payouts for new sellers may be held for up to 7 business days for security reasons. Subsequent payouts are processed within standard timeframes.
						Choose the payout method that works best for you and access your earnings quickly and hassle-free!.',
    ],
    [
        'title' => 'What fees and commissions does TrekGuider charge?',
        'text' => "TrekGuider operates with transparent and competitive fees:
						Platform Commission: 4% to 10% of the sale price, depending on your seller level. New sellers enjoy a special rate of just 5% for the first 30 days!
						Stripe Payment Processing Fee (Acquiring): Varies depending on the payment method and buyer's region. For U.S. cards, it's 2.9% + $0.30 per transaction. For international cards, it's +1.5% on top of the acquiring fee. For a complete breakdown of Stripe fees for different payment methods, please visit Stripe's Fees page.
						Payout Fee (Stripe): 0.25% + $0.25 for standard payouts, 1% for instant payouts.
						Currency Conversion Fee (Stripe, if applicable): 1% (if currency conversion is required for payout).
						Form 1099-K Issuance Fee (for U.S. Sellers): $2.99 (charged annually if your earnings exceed $600).",
    ],
    [
        'title' => 'How can I reduce my TrekGuider platform commission?',
        'text' => "Lowering your TrekGuider platform commission is easy – simply increase your sales volume! Our flexible commission tier system automatically reduces your commission rate as your sales grow. Start at Level 1 (10% commission) and advance to Level 3 (5% commission) by reaching the corresponding sales thresholds. Once you reach a commission level, it's locked in forever! Stripe payment processing fees are standard and are not controlled by TrekGuider.",
    ],
];

$faqLegal = [
    [
        'title' => 'Who is responsible for licenses, taxes, and legal permits?',
        'text' => 'As a seller on TrekGuider, you are solely responsible for complying with all applicable laws and regulations, including obtaining any necessary licenses and permits, and paying taxes according to the laws of your country and region. TrekGuider provides you with the tools to run your business, but does not assume responsibility for your legal compliance.',
    ],
    [
        'title' => 'Does TrekGuider provide tax forms and reporting for sellers?',
        'text' => "Yes, TrekGuider automatically generates and provides tax forms for U.S. sellers. If your earnings exceed $600 per year, we will automatically generate Form 1099-K and send a copy to both you and the U.S. Internal Revenue Service (IRS). You don't need to worry about creating these forms yourself. For non-U.S. sellers, TrekGuider does not provide automatic tax reporting. You are fully responsible for paying taxes and complying with tax laws in your country and, if applicable, in the U.S. Full automated tax calculation and tax reporting will be available in future updates.",
    ],
    [
        'title' => 'How do I get help and support from TrekGuider?',
        'text' => "If you have any questions or need assistance, our support team is always ready to help! The quickest way to reach us is to fill out the contact form on our Help Center page. We'll respond promptly to your inquiry and do our best to assist you.",
    ],
];
@endphp

<div class="!mb-12">
    <h4 class="text-lg md:text-xl font-bold !mb-4">Getting Started</h4>
    <x-accordion parent="#faq" :items="$faqGettingStarted"></x-accordion>
</div>

<div class="!mb-12">
    <h4 class="text-lg md:text-xl font-bold !mb-4">Selling Your Content</h4>
    <x-accordion parent="#faq" :items="$faqSelling"></x-accordion>
</div>

<div class="!mb-12">
    <h4 class="text-lg md:text-xl font-bold !mb-4">Your Creator Page & Features</h4>
    <x-accordion parent="#faq" :items="$faqCreatorPage"></x-accordion>
</div>

<div class="!mb-12">
    <h4 class="text-lg md:text-xl font-bold !mb-4">Earnings, Payouts & Fees</h4>
    <x-accordion parent="#faq" :items="$faqEarnings"></x-accordion>
</div>

<div class="">
    <h4 class="text-lg md:text-xl font-bold !mb-4">Legal & Support</h4>
    <x-accordion parent="#faq" :items="$faqLegal"></x-accordion>
</div>

                </x-card>

        </section>

        <x-invite.bottom />
    </div>
@endsection
