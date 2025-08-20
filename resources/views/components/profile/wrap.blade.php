@push('css')
    <style>
        body {
            background: var(--bg, #F9F9F9);
        }
    </style>
@endpush

@php
    $routes = auth()->user()->hasRole('creator')
        ? [
            'profile.dashboard' => ['url' => route('profile.dashboard'), 'icon' => 'dashboard', 'label' => 'Dashboard'],
            'profile' => ['url' => route('profile'), 'icon' => 'monitor', 'label' => 'Creator Page'],
            'profile.products' => ['url' => route('profile.products'), 'icon' => 'pic', 'label' => 'My Products'],
            'profile.articles' => ['url' => route('profile.articles'), 'icon' => 'document', 'label' => 'My Articles'],
            'profile.reviews' => ['url' => route('profile.reviews'), 'icon' => 'stari', 'label' => 'Reviews & Refunds'],
            'profile.sales' => ['url' => route('profile.sales'), 'icon' => 'graph', 'label' => 'Sales Analytics'],
            'profile.referal' => ['url' => route('profile.referal'), 'icon' => 'gift', 'label' => 'Referal Program'],
            'profile.purchases' => ['url' => route('profile.purchases'), 'icon' => 'bag', 'label' => 'My Purchases'],
            'profile.settings' => ['url' => route('profile.settings'), 'icon' => 'cog', 'label' => 'Account Settings'],
        ]
        : [
            'profile.purchases' => ['url' => route('profile.purchases'), 'icon' => null, 'label' => 'My Purchases'],
            'profile.referal' => ['url' => route('profile.referal'), 'icon' => null, 'label' => 'Referal Program'],
            'profile.settings' => ['url' => route('profile.settings'), 'icon' => null, 'label' => 'Account Settings'],
        ];
@endphp

<div class="the-content-wrap profile-wrap">
    <div class="container !block">
        <div class="grid grid-cols-1 lg:grid-cols-[210px_1fr] gap-3">
            <div class="col-span-1 accordion the-profile__sidebar w-full !p-0 group" id="accordionExample2">
                <div class="accordion-item p-3  !bg-active group-has-[.show]:!bg-white lg:!bg-white transition">
                    <div class="accordion-header the-profile__accord-button px-2 !bg-transparent">
                        <button 
                            class="accordion-button !text-white group-has-[.show]:!text-black !brightness-200 group-has-[.show]:!brightness-100
                                  lg:!brightness-100 lg:!text-black
                                  "
                            type="button" 
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Menu
                        </button>
                    </div>
                    <div id="collapseOne" class="accordion-collapse collapse show the-profile__wrap">
                        <div class="profile-menu-list pt-3">
                            @foreach ($routes as $name => $route)
                                <a class="{{ request()->route()->getName() === $name ? 'active' : '' }}"
                                    href="{{ $route['url'] }}">
                                    {{-- <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.00091 11.1142C11.4432 11.1079 13.5198 12.2293 14.2831 14.6432C12.7446 15.5811 10.9336 15.9423 9.00091 15.9376C7.06826 15.9423 5.25727 15.5811 3.71875 14.6432C4.48293 12.2267 6.55602 11.1079 9.00091 11.1142Z"
                                        stroke-width="1.5" stroke-linecap="square" />
                                    <circle cx="9.0023" cy="5.3773" r="3.3148" stroke-width="1.5"
                                        stroke-linecap="square" />
                                </svg> --}}
                                    @includeIf('icons.' . $route['icon'])
                                    {{ $route['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-1 overflow-scroll">
              {{ $slot }}
            </div>
        </div>
    </div>
</div>
