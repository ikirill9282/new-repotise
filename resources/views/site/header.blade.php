
<header class="@if(!auth()->check()) authorization_header @endif">
    <div class="container">
        <div class="about_block">
            <div class="logo">
                <a href="{{ route('home') }}"><img src="{{ asset('/assets/img/logo.svg') }}" alt=""></a>
            </div>
            <div class="right_group_block">
                <form class="search" method="GET" action="{{ url('/search') }}">
                    <label for="search">
                        @include('icons.search')
                    </label>
                    <input 
                      type="search" 
                      name="q"
                      placeholder="Search the site..."
                      @if(request()->has('q') && (!isset(request()->route()->parameters['slug']) || request()->route()->parameters['slug'] != 'search'))
                        value="{{ request()->get('q') }}"
                      @endif
                    >
                </form>
                <div class="hamburger-menu">
                    <input id="menu__toggle" type="checkbox" />
                    <label class="menu__btn" for="menu__toggle">
                        <span></span>
                    </label>
                    <ul class="menu__box">
                        <div class="search">
                            <label for="search">
                                @include('icons.search')
                            </label>
                            <input type="search" id="search" placeholder="Search the site...">
                        </div>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ url('/products') }}">All Products</a></li>
                        <li><a href="{{ url('/creators') }}">Creators</a></li>
                        <li class="last_menu">
                            <a href="{{ url('/insights') }}">Travel Insights</a>
                        </li>
                        <a href="#" class="profile">
                            <img src="{{ asset('/assets/img/avatar.svg') }}" alt="" class="profile_img">
                            <div class="right_text">
                                <div class="name">
                                    <h3>{{ auth()->user()?->profile }}</h3>
                                </div>
                            </div>
                        </a>
                        <div class="bottom_connect_group">
                          <a href="#" class="bottom_profile"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                              <path d="M6.99897 8.64455C8.89852 8.63969 10.5136 9.51187 11.1073 11.3893C9.91069 12.1188 8.50215 12.3998 6.99897 12.3961C5.4958 12.3998 4.08725 12.1188 2.89062 11.3893C3.48499 9.50984 5.09739 8.63969 6.99897 8.64455Z" stroke="#FC7361" stroke-linecap="square"></path>
                              <circle cx="7.00005" cy="4.18169" r="2.57818" stroke="#FC7361" stroke-linecap="square"></circle>
                            </svg>My Account</a>
                          <a href="#" class="bottom_profile"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                              <path fill-rule="evenodd" clip-rule="evenodd" d="M4.4659 11.3711C4.63506 11.3711 4.77164 11.5082 4.77164 11.6768C4.77164 11.846 4.63506 11.9831 4.4659 11.9831C4.29673 11.9831 4.16016 11.846 4.16016 11.6768C4.16016 11.5082 4.29673 11.3711 4.4659 11.3711Z" fill="#FC7361" stroke="#FC7361" stroke-linecap="square"></path>
                              <path fill-rule="evenodd" clip-rule="evenodd" d="M10.8057 11.3711C10.9749 11.3711 11.112 11.5082 11.112 11.6768C11.112 11.846 10.9749 11.9831 10.8057 11.9831C10.6366 11.9831 10.5 11.846 10.5 11.6768C10.5 11.5082 10.6366 11.3711 10.8057 11.3711Z" fill="#FC7361" stroke="#FC7361" stroke-linecap="square"></path>
                              <path d="M3.28265 3.8027H12.3971L11.6567 9.42291H3.78921L3.04427 2.01758H1.60547" stroke="#FC7361" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>My Purchases</a>
                        </div>
                        <a href="#" class="login">
                            @include('icons.user')
                            Join / Sign in
                        </a>
                    </ul>
                </div>
                <a href="#" class="all_products">
                    @include('icons.burger')
                    All Products
                </a>
                <nav class="menu">
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ url('/creators') }}">Creators</a></li>
                        <li><a href="{{ url('/insights') }}">Travel Insights</a></li>
                    </ul>
                </nav>
                @if(!auth()->check())
                  <a href="#" class="login open_auth">
                      @include('icons.user')
                      <span>Join / Sign in</span>
                  </a>
                @else
                  <a href="#" class="profile">
                    <img src="{{ url(auth()->user()?->avatar) }}" alt="avatar" class="profile_img">{{-- rounded-full w-8 --}}
                    <div class="right_text">
                      <div class="name">
                        <h3>{{ auth()->user()?->profile }}</h3>
                      </div>
                      <img src="{{ asset('assets/img/arrow_bottom.svg') }}" alt="Arrow">
                    </div>
                  </a>
                @endif
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
    </div>
</header>
