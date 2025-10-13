@extends('layouts.site')

@php
@endphp


@section('content')
  @livewire('checkout', ['order_id' => \Illuminate\Support\Facades\Crypt::encrypt($order->id)])
@endsection