@props([
  'colClass' => '',
])

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
                <div wire:ignore class="accordion-item p-3 !bg-active group-has-[.show]:!bg-white lg:!bg-white transition">
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
                                    @includeIf('icons.' . $route['icon'])
                                    {{ $route['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-1 min-w-0 {{ $colClass }}">
              {{ $slot }}
            </div>
        </div>
    </div>
</div>
