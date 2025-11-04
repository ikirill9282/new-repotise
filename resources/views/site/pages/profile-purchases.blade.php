@extends('layouts.site')

@section('content')
    <x-profile.wrap>
        <div class="the-content__content">
            @if(!$user->hasRole('creator'))
              <x-profile.begin-verify class="mb-4" />
            @endif

            @livewire('profile.tables', [
              'active' => isset($type) && $type == 'subscriptions' ? 'subs' : 'orders',
              'args' => [
                'user_id' => \Illuminate\Support\Facades\Crypt::encrypt($user->id),
              ],
              'tables' => [
                [
                  'name' => "orders",
                  'title' => "Products",
                  'href' => route('profile.purchases')
                ],
                [
                  'name' => 'subs',
                  'title' => "Subscriptions",
                  'href' => route('profile.purchases.subscriptions', ['type' => 'subscriptions'])
                ],
              ],
            ])
        </div>
    </x-profile.wrap>
@endsection
