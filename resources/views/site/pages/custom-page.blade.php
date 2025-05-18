@extends('layouts.site')

@section('content')
    @foreach ($page->config->sortBy('id') as $conf)
      <div class="container !mx-auto py-4 custom-section all-policies">
        {!! $conf->value !!}
      </div>
    @endforeach
@endsection