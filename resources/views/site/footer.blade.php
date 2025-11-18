<footer class="">
    <div class="container !mx-auto">
        <nav class="top_menu_group">
            <div class="flex-col md:flex-row w-full flex justify-between">
                <div class="group mb-4 md:mb-0">
                    <h3 class="flex justify-between !mb-4"><span>Explore</span> <span
                            class="stroke-[#A4A0A0] rotate-180 transition md:hidden">@include('icons.arrow_footer')</span></h3>
                    <ul class="overflow-hidden">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('products') }}">All Products</a></li>
                        <li><a href="{{ route('favorites') }}" class="{{ auth()->check() ? '' : 'open_auth' }}">Favorite</a></li>
                        <li><a href="{{ route('creators') }}">Creators</a></li>
                        <li><a href="{{ route('insights') }}">Travel Insights</a></li>
                    </ul>
                </div>
                <div class="group mb-4 md:mb-0">
                    <h3 class="flex justify-between !mb-4"><span>Partnerships</span> <span
                            class="stroke-[#A4A0A0] rotate-180 transition md:hidden">@include('icons.arrow_footer')</span></h3>
                    <ul class="overflow-hidden">
												<li>
													<a href="{{ auth()->check() && auth()->user()->hasRole('creator') ? url('/profile/dashboard') : route('sellers') }}">
														For Creators
													</a>
												</li>

                        <li><a href="{{ route('investments') }}">For Investors & Partners</a></li>
                        <li><a href="{{ route('referal') }}">Referral Program</a></li>
                        <li><a href="{{ route('help-center') }}">Help Center</a></li>
                    </ul>
                </div>
                <div class="group mb-4 hidden md:block md:mb-0">
                    <h3 class="flex justify-between !mb-4"><span>Legal</span> </h3>
                    <ul class="overflow-hidden">
                        <li><a href="{{ url('/policies/terms-and-conditions') }}">Terms and Conditions</a></li>
                        <li><a href="{{ url('/policies/privacy-policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ url('/policies/cookie-policy') }}">Cookie Policy</a></li>
                        <li><a href="{{ url('/policies-all') }}">More Policies</a></li>
                    </ul>
                </div>
                <div x-data="{}" class="group mb-4 md:mb-0">
                    <h3 class="flex justify-between !mb-4"><span>My Account</span> <span
                            class="stroke-[#A4A0A0] rotate-180 transition md:hidden">@include('icons.arrow_footer')</span></h3>
                    <ul class="overflow-hidden">
                        @if (auth()->check())
                          <li><a href="{{ auth()->user()->makeProfileUrl() }}" class="">My Profile</a></li>
                        @else
                          <li><a @click.prevent="$dispatch('openModal', {modalName: 'auth'})"  href="#">Join / Sign In</a></li>
                          <li><a @click.prevent="$dispatch('openModal', {modalName: 'reset-password'})" href="#" class="reset_password">Forgot Password?</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
				
        <div class="bottom_connecting_group">
            <div class="logo">
                <a href="{{ route('home') }}">
                    @include('icons.footer_logo')
                </a>
            </div>
            <div class="connecting">
                <a href="https://www.facebook.com/TrekGuider" target="_blank" rel="noopener noreferrer" class="first_connect transition !duration-500 hover:!text-blue-500">
                    @include('icons.facebook')
                </a>
                <a href="https://x.com/TrekGuider" target="_blank" rel="noopener noreferrer" class="second_connect transition !duration-500 hover:!text-black">
                    @include('icons.twitter')
                </a>
                <a href="https://discord.gg/egw9MYTJbu" target="_blank" rel="noopener noreferrer" class="third_connect transition !duration-500 hover:!text-[#5865F2]">
								<svg xmlns="http://www.w3.org/2000/svg"
     width="48" height="48"
     viewBox="0 0 1024 1024">
  <circle cx="512" cy="512" r="512" fill="#4A4A4A"/>

  <path fill="#FFFFFF" d="
    M689.43 349
    a422.21 422.21 0 0 0-104.22-32.32
    1.58 1.58 0 0 0-1.68.79
    294.11 294.11 0 0 0-13 26.66
    389.78 389.78 0 0 0-117.05 0
    269.75 269.75 0 0 0-13.18-26.66
    1.64 1.64 0 0 0-1.68-.79
    A421 421 0 0 0 334.44 349
    a1.49 1.49 0 0 0-.69.59
    c-66.37 99.17-84.55 195.9-75.63 291.41
    a1.76 1.76 0 0 0 .67 1.2
    424.58 424.58 0 0 0 127.85 64.63
    1.66 1.66 0 0 0 1.8-.59
    303.45 303.45 0 0 0 26.15-42.54
    1.62 1.62 0 0 0-.89-2.25
    279.6 279.6 0 0 1-39.94-19
    1.64 1.64 0 0 1-.16-2.72
    c2.68-2 5.37-4.1 7.93-6.22
    a1.58 1.58 0 0 1 1.65-.22
    c83.79 38.26 174.51 38.26 257.31 0
    a1.58 1.58 0 0 1 1.68.2
    c2.56 2.11 5.25 4.23 8 6.24
    a1.64 1.64 0 0 1-.14 2.72
    262.37 262.37 0 0 1-40 19
    1.63 1.63 0 0 0-.87 2.28
    340.72 340.72 0 0 0 26.13 42.52
    1.62 1.62 0 0 0 1.8.61
    423.17 423.17 0 0 0 128-64.63
    1.64 1.64 0 0 0 .67-1.18
    c10.68-110.44-17.88-206.38-75.7-291.42
    a1.3 1.3 0 0 0-.63-.63
    z
    M427.09 582.85
    c-25.23 0-46-23.16-46-51.6
    s20.38-51.6 46-51.6
    c25.83 0 46.42 23.36 46 51.6
    .02 28.44-20.37 51.6-46 51.6
    z
    m170.13 0
    c-25.23 0-46-23.16-46-51.6
    s20.38-51.6 46-51.6
    c25.83 0 46.42 23.36 46 51.6
    .01 28.44-20.17 51.6-46 51.6
    z"/>
</svg>
                </a>
            </div>
        </div>
        <nav class="menu_bottom">
            <ul>
                <li><a href="{{ url('/policies/terms-and-conditions') }}">Terms and Conditions</a></li>
                <li><a href="{{ url('/policies/privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ url('/policies/cookie-policy') }}">Cookie Policy</a></li>
                <li><a href="{{ url('/policies-all') }}">More Policies</a></li>
            </ul>
        </nav>

        <div class="bottom_by_des !mb-10">
          <span class="TrekGuider_span">{{ date('Y', time()) }} TrekGuider Ink.</span>
        </div>

        <div class="text-gray font-thin"><span class="inline-block">Designed by</span><a href="https://milksite.ru/"><span class="inline-block text-white !px-3 !py-1 bg-[#046D53] rounded-lg !ml-3">moloko69</span></a></div>
    </div>
</footer>