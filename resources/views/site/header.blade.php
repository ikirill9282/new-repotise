<header>
  <div class="container">
      <div class="about_block">
          <div class="logo">
              <a href="{{ route('home') }}"><img src="{{ asset('/assets/img/logo.svg') }}" alt=""></a>
          </div>
          <div class="search">
              <label for="search">
                @include('icons.search')
              </label>
              <input type="search" id="search" placeholder="Поиск по сайту">
          </div>
          <a href="#" class="all_products">
              @include('icons.burger')
              All products
          </a>
          <ul class="menu">
              <li><a href="{{ route('home') }}">Home</a></li>
              <li><a href="{{ url('/articles') }}">Travel Insights</a></li>
              <li><a href="{{ url('/news') }}">Travel News</a></li>
          </ul>
          <a href="#" class="profile">
              <img src="{{ asset('/assets/img/avatar.svg') }}" alt="avatar" class="profile_img">
              <div class="right_text">
                  <div class="name">
                      <h3>@talmaev1</h3>
                      <span>Покупатель</span>
                  </div>
                  <img src="{{ asset('/assets/img/arrow_bottom.svg') }}" alt="arrow_bottom">
              </div>
          </a>
          <a href="#" class="like rection_groups">
              @include('icons.favorite')
              <span>10</span>
          </a>
          <a href="#" class="basket rection_groups">
              @include('icons.cart')
              <span>10</span>
          </a>
      </div>
  </div>
</header>