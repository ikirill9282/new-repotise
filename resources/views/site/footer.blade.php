<footer class="">
    <div class="container !mx-auto">
        <nav class="top_menu_group">
            <div class="flex-col md:flex-row w-full flex justify-between">
                <div class="group mb-4 md:mb-0">
                    <h3 class="flex justify-between !mb-4"><span>Explore</span> <span
                            class="stroke-[#A4A0A0] rotate-180 transition md:hidden">@include('icons.arrow_footer')</span></h3>
                    <ul class="overflow-hidden">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ url('/products') }}">All Products</a></li>
                        <li><a href="{{ url('/profile/favorites') }}">Favourite</a></li>
                        <li><a href="{{ url('/creators') }}">Creators</a></li>
                        <li><a href="{{ url('/insights') }}">Travel Insights</a></li>
                    </ul>
                </div>
                <div class="group mb-4 md:mb-0">
                    <h3 class="flex justify-between !mb-4"><span>Partnerships</span> <span
                            class="stroke-[#A4A0A0] rotate-180 transition md:hidden">@include('icons.arrow_footer')</span></h3>
                    <ul class="overflow-hidden">
                        <li><a href="{{ url('/partner/creators') }}">For Creators</a></li>
                        <li><a href="{{ url('/partner/investors') }}">For Investors & Partners</a></li>
                        <li><a href="{{ url('/partner/referal') }}">Referral Program</a></li>
                        <li><a href="{{ url('/help-center') }}">Help Center</a></li>
                    </ul>
                </div>
                <div class="group mb-4 hidden md:block md:mb-0">
                    <h3 class="flex justify-between !mb-4"><span>Legal</span> </h3>
                    <ul class="overflow-hidden">
                        <li><a href="{{ url('/terms-and-conditions') }}">Terms and Conditions</a></li>
                        <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ url('/cookie-policy') }}">Cookie Policy</a></li>
                        <li><a href="{{ url('/all-policies') }}">More Policies</a></li>
                    </ul>
                </div>
                <div class="group mb-4 md:mb-0">
                    <h3 class="flex justify-between !mb-4"><span>My Account</span> <span
                            class="stroke-[#A4A0A0] rotate-180 transition md:hidden">@include('icons.arrow_footer')</span></h3>
                    <ul class="overflow-hidden">
                        <li><a href="#" class="open_auth">Join / Sign In</a></li>
                        <li><a href="#" class="">Forgot Password?</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="bottom_connecting_group">
            <div class="logo">
                <a href="#">
                    @include('icons.footer_logo')
                </a>
            </div>
            <div class="connecting">
                <a href="#" class="first_connect transition">
                    @include('icons.facebook')
                </a>
                <a href="#" class="second_connect transition">
                    @include('icons.twitter')
                </a>
            </div>
        </div>
        <nav class="menu_bottom">
            <ul>
                <li><a href="{{ url('/terms-and-conditions') }}">Terms and Conditions</a></li>
                <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ url('/cookie-policy') }}">Cookie Policy</a></li>
                <li><a href="{{ url('/all-policies') }}">More Policies</a></li>
            </ul>
        </nav>
        <div class="bottom_by_des">
            <span class="TrekGuider_span">2025 TrekGuider Ink.</span>
            <span class="by_to">by moloko69.ru</span>
        </div>
    </div>
</footer>