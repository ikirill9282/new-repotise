@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    @livewire('profile.page', ['user' => $user, 'container' => '!px-0'])
  </x-profile.wrap>
@endsection
