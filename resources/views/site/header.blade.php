<header class="@if (!auth()->check()) authorization_header @else authorized @endif w-full !z-[120] bg-white sticky top-0 left-0">
    <div class="container !mx-auto">
        <div class="about_block">
            <div class="logo">
                <a href="{{ route('home') }}"><img class="max-w-18 sm:!max-w-none"
                        src="{{ asset('/assets/img/logo.svg') }}" alt=""></a>
            </div>
            <div x-data="{}" class="right_group_block gap-3">
                <x-search 
                  placeholder="Search the site..."
                  hits="header_hits"
                  :button="false"
                />

                <div class="hamburger-menu" data-open="false">
                    <input id="menu__toggle" type="checkbox" class="w-0 h-0" />
                    <div class="menu__btn">
                        <span></span>
                    </div>
                </div>
                <a href="{{ route('products') }}" class="all_products">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="transparent">
                    <path d="M5 10H7C9 10 10 9 10 7V5C10 3 9 2 7 2H5C3 2 2 3 2 5V7C2 9 3 10 5 10Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 10H19C21 10 22 9 22 7V5C22 3 21 2 19 2H17C15 2 14 3 14 5V7C14 9 15 10 17 10Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 22H19C21 22 22 21 22 19V17C22 15 21 14 19 14H17C15 14 14 15 14 17V19C14 21 15 22 17 22Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 22H7C9 22 10 21 10 19V17C10 15 9 14 7 14H5C3 14 2 15 2 17V19C2 21 3 22 5 22Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-nowrap">All Products</span>
                </a>
                <nav class="menu">
                    <ul class="justify-end">
                        <li><a class="text-nowrap" href="{{ route('home') }}">Home</a></li>
                        <li><a class="text-nowrap" href="{{ route('creators') }}">Creators</a></li>
                        <li><a class="text-nowrap" href="{{ route('insights') }}">Travel Insights</a></li>
                    </ul>
                </nav>
                @if (!auth()->check())
                    <a @click.prevent="$dispatch('openModal', {modalName: 'cart'})" href="#" class="cart">
                        @include('icons.cart')
                        <span class="cart-counter @if (!$cart->getCartCount()) hidden @endif">{{ $cart->getCartCount() }}</span>
                    </a>
                    <a href="#" class="login open_auth">
                        @include('icons.user')
                        <span class="text-nowrap">Join / Sign in</span>
                    </a>
                @else
                    @php
                        $baseMenu = auth()->user()->hasRole('creator')
                            ? [
                                ['route' => 'profile.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
                                ['route' => 'profile', 'icon' => 'monitor', 'label' => 'Creator Page'],
                                ['route' => 'profile.products', 'icon' => 'pic', 'label' => 'My Products'],
                                ['route' => 'profile.articles', 'icon' => 'document', 'label' => 'My Articles'],
                                ['route' => 'profile.reviews', 'icon' => 'stari', 'label' => 'Reviews & Refunds'],
                                ['route' => 'profile.sales', 'icon' => 'graph', 'label' => 'Sales Analytics'],
                                ['route' => 'profile.referal', 'icon' => 'gift', 'label' => 'Referal Program'],
                                ['route' => 'profile.purchases', 'icon' => 'bag', 'label' => 'My Purchases'],
                                ['route' => 'profile.settings', 'icon' => 'cog', 'label' => 'Account Settings'],
                            ]
                            : [
                                ['route' => 'profile.purchases', 'icon' => 'bag', 'label' => 'My Purchases'],
                                ['route' => 'profile.referal', 'icon' => 'gift', 'label' => 'Referal Program'],
                                ['route' => 'profile.settings', 'icon' => 'cog', 'label' => 'Account Settings'],
                            ];

                        $profileMenu = array_map(function ($item) {
                            return [
                                'url' => route($item['route']),
                                'icon' => $item['icon'],
                                'label' => $item['label'],
                            ];
                        }, $baseMenu);

                        $profileMenu[] = [
                            'url' => route('favorites'),
                            'icon' => 'favorite',
                            'label' => 'Favorites',
                        ];
                    @endphp
                    <div class="profile-dropdown group">
                        <a href="{{ route('profile') }}" class="profile">
                            <img src="{{ auth()->user()?->avatar }}" alt="avatar"
                                class="profile_img">
                            <div class="right_text">
                                <div class="name flex flex-col">
                                    <h3>{{ auth()->user()?->profile }}</h3>
                                </div>
                                <img class="profile-dropdown__arrow" src="{{ asset('assets/img/arrow_bottom.svg') }}" alt="Arrow">
                            </div>
                        </a>
                        <div class="profile-dropdown__menu">
                            <ul>
                                @foreach ($profileMenu as $item)
                                    @php
                                        $iconParams = ['width' => 16, 'height' => 16];
                                        if ($item['icon'] === 'favorite') {
                                            $iconParams['stroke'] = 'currentColor';
                                        }
                                    @endphp
                                    <li>
                                        <a href="{{ $item['url'] }}">
                                            @includeIf('icons.' . $item['icon'], $iconParams)
                                            <span>{{ $item['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                                <li class="profile-dropdown__divider"></li>
                                <li>
                                    <a href="{{ route('signout') }}" class="profile-dropdown__logout">
                                        @include('icons.logout', ['width' => 16, 'height' => 16])
                                        <span>Sign out</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <a href="{{ route('favorites') }}" class="like rection_groups !text-transparent">
                        @include('icons.favorite')
                        <span class="favorite-counter @if (!auth()->user()->favorite_count) hidden @endif" >{{ auth()->user()->favorite_count }}</span>
                    </a>
                    <a @click.prevent="$dispatch('openModal', {modalName: 'cart'})" href="#" class="basket rection_groups">
                        @include('icons.cart')
                        <span class="cart-counter @if (!$cart->getCartCount()) hidden @endif">{{ $cart->getCartCount() }}</span>
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
            <form class="search relative search-form" method="GET" action="{{ route('search') }}">
                <label for="search">
                    @include('icons.search')
                </label>
                <input type="search" name="q" class="search-input" id="search" data-hits="menu-search"
                    autocomplete="off" placeholder="Search the site...">
                @include('site.components.hits', ['id' => 'menu-search'])
            </form>
            <div class="search-error text-sm text-red-500 mt-2 hidden"></div>
            <ul class="mob_menu">
                <li><a class="text-nowrap" href="{{ route('home') }}">Home</a></li>
                <li><a class="text-nowrap" href="{{ route('products') }}">All Products</a></li>
                <li><a class="text-nowrap" href="{{ route('creators') }}">Creators</a></li>
                <li class="last_menu">
                    <a class="text-nowrap" href="{{ route('insights') }}">Travel Insights</a>
                </li>
            </ul>
            @if (auth()->check())
                <a href="{{ route('profile') }}" class="profile flex items-center gap-2">
                    <img src="{{ auth()->user()?->avatar }}" alt="" class="profile_img">
                    <div class="right_text">
                        <div class="name flex flex-col">
                            <h3>{{ auth()->user()?->profile }}</h3>
                            {{-- <div class="text-sm">{{ currency(auth()->user()?->balance) }}</div> --}}
                        </div>
                    </div>
                </a>
                <div class="bottom_connect_group">
                    <a href="{{ route('profile') }}" class="bottom_profile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"
                            fill="none">
                            <path
                                d="M6.99897 8.64455C8.89852 8.63969 10.5136 9.51187 11.1073 11.3893C9.91069 12.1188 8.50215 12.3998 6.99897 12.3961C5.4958 12.3998 4.08725 12.1188 2.89062 11.3893C3.48499 9.50984 5.09739 8.63969 6.99897 8.64455Z"
                                stroke="#FC7361" stroke-linecap="square"></path>
                            <circle cx="7.00005" cy="4.18169" r="2.57818" stroke="#FC7361"
                                stroke-linecap="square">
                            </circle>
                        </svg>My Account
                    </a>

                    <a href="{{ route('profile.purchases') }}" class="bottom_profile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"
                            fill="none">
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
                <a href="#" class="login flex gap-2 items-center open_auth">
                    @include('icons.user')
                    Join / Sign in
                </a>
            @endif
        </div>
    </nav>
</aside>
