@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    @livewire('profile.edit')
  </x-profile.wrap>
@endsection
