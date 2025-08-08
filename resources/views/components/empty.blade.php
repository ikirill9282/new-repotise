@props([
  'class' => '',
  'title' => '',
])

<div class="{{ $class }}  empty-block" role="tabpanel" aria-labelledby="pills-home-tab">
  <div class="top_group_fav">
      <div class="left_text">
          <h2>{{ $title }}</h2>
          <div class="bottom_text">
              <a href="{{ route('products') }}" class="discover">Discover&nbsp;Now</a>
              <a href="{{ route('creators') }}" class="connect">Connect with Creators</a>
          </div>
      </div>
      <img src="{{ asset('assets/img/women_img.png') }}" alt="Women" class="women_img">
  </div>
</div>