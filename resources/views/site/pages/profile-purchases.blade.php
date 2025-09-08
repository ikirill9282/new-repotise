@extends('layouts.site')

@section('content')
    <x-profile.wrap>
        <div class="the-content__content">
            @if(!$user->verified)
              <x-profile.begin-verify class="mb-4" />
            @endif

            <x-card>
              @livewire('profile.tables', [
                'active' => 'orders',
                'tables' => [
                  [
                    'name' => "orders",
                    'title' => "Products",
                  ],
                  [
                    'name' => 'subs',
                    'title' => "Subscriptions",
                  ],
                ]
              ])
            </x-card>
        </div>
    </x-profile.wrap>
@endsection
