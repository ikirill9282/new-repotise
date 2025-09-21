@extends('layouts.site')

@section('content')
  <section class="!py-12" id="create-article">
      <div class="container">
        @livewire('forms.product', [
          'product_id' => $product_id,
        ])
      </div>
  </section>
@endsection
