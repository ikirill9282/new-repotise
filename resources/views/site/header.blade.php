<header class="authorization_header">
    <div class="container">
        <div class="about_block">
            <div class="logo">
                <a href="{{ route('home') }}"><img src="{{ asset('/assets/img/logo.svg') }}" alt=""></a>
            </div>
            <div class="right_group_block">
                <div class="search">
                    <label for="search">
                        @include('icons.search')
                    </label>
                    <input type="search" placeholder="Search the site...">
                </div>
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
                            <a href="{{ url('/articles') }}">Travel Insights</a>
                        </li>
                        <a href="#" class="profile">
                            <img src="{{ asset('/assets/img/avatar.svg') }}" alt="" class="profile_img">
                            <div class="right_text">
                                <div class="name">
                                    <h3>@talmaev1</h3>
                                </div>
                            </div>
                        </a>
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
                        <li><a href="{{ url('/articles') }}">Travel Insights</a></li>
                    </ul>
                </nav>
                <a href="#" class="login">
                    @include('icons.user')
                    <span>Join / Sign in</span>
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
    </div>
</header>
