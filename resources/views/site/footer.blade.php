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
                        <li><a href="{{ route('sellers') }}">For Creators</a></li>
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
                <a href="#" class="first_connect transition !duration-500 hover:!text-blue-500">
                    @include('icons.facebook')
                </a>
                <a href="#" class="second_connect transition !duration-500 hover:!text-black">
                    @include('icons.twitter')
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

        <div class="text-gray font-thin"><span class="inline-block">Designed by</span><span class="inline-block text-white !px-3 !py-1 bg-[#046D53] rounded-lg !ml-3">moloko69</span></div>
    </div>
</footer>