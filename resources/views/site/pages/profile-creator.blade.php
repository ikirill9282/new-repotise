@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    @livewire('profile.page', ['user' => $user])
  </x-profile.wrap>
@endsection
