@extends('layouts.site')

@php
@endphp


@section('content')
  @livewire('checkout', ['order' => $order])
@endsection