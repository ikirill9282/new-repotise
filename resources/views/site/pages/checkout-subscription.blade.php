@extends('layouts.site')

@section('content')
  @livewire('checkout-subscription', ['order_id' => \Illuminate\Support\Facades\Crypt::encrypt($order->id)])
@endsection