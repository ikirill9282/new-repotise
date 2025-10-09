@extends('layouts.site')

@section('content')
    <x-profile.wrap>
        <div class="the-content__content">
            @if(!$user->verified)
              <x-profile.begin-verify class="mb-4" />
            @endif
            
            @livewire('profile.tables', [
              'active' => 'orders',
              'args' => [
                'user_id' => \Illuminate\Support\Facades\Crypt::encrypt($user->id),
              ],
              'tables' => [
                [
                  'name' => "orders",
                  'title' => "Products",
                ],
                [
                  'name' => 'subs',
                  'title' => "Subscriptions",
                ],
              ],
            ])
        </div>
    </x-profile.wrap>
@endsection
