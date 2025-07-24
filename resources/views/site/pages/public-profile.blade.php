@extends('layouts.site')

@section('content')
  <div class="container">
    <h1>Public Profile <i>«{{ $user->getName() }}»</i></h1>
  </div>
@endsection