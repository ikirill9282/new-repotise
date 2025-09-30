@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    @livewire('profile.page', ['user_id' => $user_id, 'container' => '!px-0'])
  </x-profile.wrap>
@endsection
