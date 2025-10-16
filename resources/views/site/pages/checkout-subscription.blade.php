@extends('layouts.site')

@section('content')
  @livewire('checkout-subscription', [
    'product_id' => $data['product_id'],
    'period' => $data['period'],
  ]);
@endsection