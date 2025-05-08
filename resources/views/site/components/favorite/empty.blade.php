<div class="{{ isset($class) ? $class : '' }}  empty-block" role="tabpanel" aria-labelledby="pills-home-tab">
  <div class="top_group_fav">
      <div class="left_text">
          <h2>Your {{ isset($text) ? $text : 'Favorites list' }} is currently empty. Start adding <br> products you love!</h2>
          <div class="bottom_text">
              <a href="{{ url('/products') }}" class="discover">Discover&nbsp;Now</a>
              <a href="{{ url('/creators') }}" class="connect">Connect with Creators</a>
          </div>
      </div>
      <img src="{{ asset('assets/img/women_img.png') }}" alt="Women" class="women_img">
  </div>
</div>