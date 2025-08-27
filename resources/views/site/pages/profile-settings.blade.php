@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    <x-card>
      @livewire('profile.settings')
    </x-card>
  </x-profile.wrap>
@endsection