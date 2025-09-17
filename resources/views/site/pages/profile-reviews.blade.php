@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    <div class="">
      <x-card size="sm" class="mb-10">
        @livewire('profile.tables.profile-reviews')
      </x-card>
      <x-card size="sm" class="">
        @livewire('profile.tables.profile-refunds')
      </x-card>
    </div>
  </x-profile.wrap>
@endsection