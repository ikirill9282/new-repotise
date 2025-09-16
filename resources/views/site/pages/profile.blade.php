@extends('layouts.site')


@section('content')
    <div class="!py-6 bg-light">
      @livewire('profile.page', ['user' => $user])
    </div>
@endsection
