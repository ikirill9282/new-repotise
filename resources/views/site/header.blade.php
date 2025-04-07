<header class="@if (!auth()->check()) authorization_header @else authorized @endif w-full !z-[120] bg-white">
    <div class="container !mx-auto">
        <div class="about_block">
            <div class="logo">
                <a href="{{ route('home') }}"><img class="max-w-18 sm:!max-w-none"
                        src="{{ asset('/assets/img/logo.svg') }}" alt=""></a>
            </div>
            <div class="right_group_block gap-2">
                <form class="search relative w-full max-w-full" method="GET" action="{{ url('/search') }}">
                    <label for="search">
                        @include('icons.search')
                    </label>
                    <input type="search" name="q" placeholder="Search the site..." class="search-input"
                        autocomplete="off" data-hits="header_hits"
                        @if (request()->has('q') &&
                                (!isset(request()->route()->parameters['slug']) || request()->route()->parameters['slug'] != 'search')) value="{{ request()->get('q') }}" @endif>
                    @include('site.components.hits', ['id' => 'header_hits'])
                </form>

                <div class="hamburger-menu" data-open="false">
                    <input id="menu__toggle" type="checkbox" class="w-0 h-0" />
                    <div class="menu__btn">
                        <span></span>
                    </div>
                </div>
                <a href="#" class="all_products">
                    @include('icons.burger')
                    <span class="text-nowrap">All Products</span>
                </a>
                <nav class="menu">
                    <ul class="justify-end">
                        <li><a class="text-nowrap" href="{{ route('home') }}">Home</a></li>
                        <li><a class="text-nowrap" href="{{ url('/creators') }}">Creators</a></li>
                        <li><a class="text-nowrap" href="{{ url('/insights') }}">Travel Insights</a></li>
                    </ul>
                </nav>
                @if (!auth()->check())
                    <a href="#" class="login open_auth">
                        @include('icons.user')
                        <span class="text-nowrap">Join / Sign in</span>
                    </a>
                @else
                    <a href="#" class="profile">
                        <img src="{{ url(auth()->user()?->avatar) }}" alt="avatar"
                            class="profile_img">{{-- rounded-full w-8 --}}
                        <div class="right_text">
                            <div class="name">
                                <h3>{{ auth()->user()?->profile }}</h3>
                            </div>
                            <img src="{{ asset('assets/img/arrow_bottom.svg') }}" alt="Arrow">
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
                @endif
            </div>
        </div>
    </div>
</header>

<aside class="fixed top-0 right-0 z-[140] w-screen h-screen bg-white transition duration-300 translate-x-full lg:hidden"
    id="mobile_menu" data-open="false">
    <nav class="container">
        <div class="flex flex-col">
            <div class="menu__btn self-end !static menu_open" id="close_menu">
                <span></span>
            </div>
            <form class="search relative" method="GET" action="{{ url('/search') }}">
                <label for="search">
                    @include('icons.search')
                </label>
                <input type="search" name="q" class="search-input" id="search" data-hits="menu-search"
                    autocomplete="off" placeholder="Search the site...">
                @include('site.components.hits', ['id' => 'menu-search'])
            </form>
            <ul class="mob_menu">
                <li><a class="text-nowrap" href="{{ route('home') }}">Home</a></li>
                <li><a class="text-nowrap" href="{{ url('/products') }}">All Products</a></li>
                <li><a class="text-nowrap" href="{{ url('/creators') }}">Creators</a></li>
                <li class="last_menu">
                    <a class="text-nowrap" href="{{ url('/insights') }}">Travel Insights</a>
                </li>
            </ul>
            @if (auth()->check())
            <a href="#" class="profile flex items-center gap-2">
                <img src="{{ asset('/assets/img/avatar.svg') }}" alt="" class="profile_img">
                <div class="right_text">
                    <div class="name">
                        <h3>{{ auth()->user()?->profile }}</h3>
                    </div>
                </div>
            </a>
            <div class="bottom_connect_group">
                <a href="#" class="bottom_profile"><svg xmlns="http://www.w3.org/2000/svg" width="14"
                        height="14" viewBox="0 0 14 14" fill="none">
                        <path
                            d="M6.99897 8.64455C8.89852 8.63969 10.5136 9.51187 11.1073 11.3893C9.91069 12.1188 8.50215 12.3998 6.99897 12.3961C5.4958 12.3998 4.08725 12.1188 2.89062 11.3893C3.48499 9.50984 5.09739 8.63969 6.99897 8.64455Z"
                            stroke="#FC7361" stroke-linecap="square"></path>
                        <circle cx="7.00005" cy="4.18169" r="2.57818" stroke="#FC7361" stroke-linecap="square">
                        </circle>
                    </svg>My Account</a>
                <a href="#" class="bottom_profile"><svg xmlns="http://www.w3.org/2000/svg" width="14"
                        height="14" viewBox="0 0 14 14" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M4.4659 11.3711C4.63506 11.3711 4.77164 11.5082 4.77164 11.6768C4.77164 11.846 4.63506 11.9831 4.4659 11.9831C4.29673 11.9831 4.16016 11.846 4.16016 11.6768C4.16016 11.5082 4.29673 11.3711 4.4659 11.3711Z"
                            fill="#FC7361" stroke="#FC7361" stroke-linecap="square"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M10.8057 11.3711C10.9749 11.3711 11.112 11.5082 11.112 11.6768C11.112 11.846 10.9749 11.9831 10.8057 11.9831C10.6366 11.9831 10.5 11.846 10.5 11.6768C10.5 11.5082 10.6366 11.3711 10.8057 11.3711Z"
                            fill="#FC7361" stroke="#FC7361" stroke-linecap="square"></path>
                        <path d="M3.28265 3.8027H12.3971L11.6567 9.42291H3.78921L3.04427 2.01758H1.60547"
                            stroke="#FC7361" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>My Purchases</a>
            </div>
            @else
            <a href="#" class="login flex gap-2 items-center">
                @include('icons.user')
                Join / Sign in
            </a>
            @endif
        </div>
    </nav>
</aside>

@push('js')
    <script>
        const header = $('header');
        const headerHeight = header.outerHeight();
        // const start = (document.querySelector('.parallax')) ? $('.parallax').outerHeight() : headerHeight;
        
        let lastPoint = 0;

        $(window).on('scroll', function(evt) {
            const point = $(this).scrollTop();
            if (point > (headerHeight + 10)) {
                if (!header.hasClass('!sticky')) {
                    header.addClass('!sticky top-0 left-0 translate-y-[-100%] shadow-md');
                }
                if (point <= lastPoint) {
                    header.addClass('transition !translate-y-0');
                } else if (!$('#mobile_menu').data('open')) {
                    header.removeClass('!translate-y-0');
                }
            } else if (document.querySelector('.parallax')) {
                // header.removeClass('!translate-y-0');
                // setTimeout(() => {
                //     header.removeClass('!sticky transition top-0 left-0 translate-y-[-100%] shadow-md');
                // }, 100);
                // header.removeClass('!sticky top-0 left-0 translate-y-[-100%] shadow-md');
                // header.removeClass('transition');
            }

            if (point == 0) {
                header.removeClass('!sticky top-0 left-0 translate-y-[-100%] shadow-md');
                header.removeClass('transition');
            }

            lastPoint = point;
        });

        $('.hamburger-menu').on('click', function(evt) {
            const menu = $('#mobile_menu');
            const button = $(this).find('.menu__btn');

            menu.removeClass('translate-x-full');
            menu.data('open', true);
            $(button).toggleClass('menu_open');
            // $('#close_menu').toggleClass('menu_open');

            if (menu.data('open')) {
                $('body').addClass('overflow-hidden');
            } else {
                $('body').removeClass('overflow-hidden');
            }
        });

        $('#close_menu').on('click', function(evt) {
            const menu = $('#mobile_menu');
            const button = $('.hamburger-menu').find('.menu__btn');
            menu.data('open', false);

            menu.addClass('translate-x-full');
            $(button).toggleClass('menu_open');
            // $('#close_menu').toggleClass('menu_open');

            if (menu.data('open')) {
                $('body').addClass('overflow-hidden');
            } else {
                $('body').removeClass('overflow-hidden');
            }
        });
    </script>
@endpush
