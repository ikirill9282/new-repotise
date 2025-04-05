
<footer>
  <div class="container">
      <nav class="top_menu_group">
          <ul>
              <h3>Explore <img src="{{ asset('assets/img/arrow_footer.svg') }}" alt="Arrow"></h3>
              <li><a href="{{ route('home') }}">Home</a></li>
              <li><a href="{{ url('/products') }}">All Products</a></li>
              <li><a href="{{ url('/profile/favorites') }}">Favourite</a></li>
              <li><a href="{{ url('/creators') }}">Creators</a></li>
              <li><a href="{{ url('/insights') }}">Travel Insights</a></li>
          </ul>
          <ul>
              <h3>Partnerships <img src="{{ asset('assets/img/arrow_footer.svg') }}" alt="Arrow"></h3>
              <li><a href="{{ url('/partner/creators') }}">For Creators</a></li>
              <li><a href="{{ url('/partner/investors') }}">For Investors & Partners</a></li>
              <li><a href="{{ url('/partner/referal') }}">Referral Program</a></li>
              <li><a href="{{ url('/help-center') }}">Help Center</a></li>
          </ul>
          <ul>
              <h3>Legal</h3>
              <li><a href="{{ url('/terms-and-conditions') }}">Terms and Conditions</a></li>
              <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
              <li><a href="{{ url('/cookie-policy') }}">Cookie Policy</a></li>
              <li><a href="{{ url('/all-policies') }}">More Policies</a></li>
          </ul>
          <ul>
              <h3>My Account <img src="{{ asset('assets/img/arrow_footer.svg') }}" alt="Arrow"></h3>
              <li><a href="#" class="open_auth">Join / Sign In</a></li>
              <li><a href="#" class="">Forgot Password?</a></li>
          </ul>
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
              <li><a href="#">Terms and Conditions</a></li>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Cookie Policy</a></li>
              <li><a href="#">More Policies</a></li>
          </ul>
      </nav>
      <div class="bottom_by_des">
          <span class="TrekGuider_span">2025 TrekGuider Ink.</span>
          <span class="by_to">by moloko69.ru</span>
      </div>
  </div>
</footer>