@extends('layouts.site')

@section('content')
  <div class="container">
    <div class="!py-10">
      @foreach ($models as $model)
        <div class="flex flex-col !gap-3">
          <x-link href="/policies/{{ $model->slug }}" class="!border-none sm:!text-lg">{{ $model->title }}</x-link>
        </div>
      @endforeach
    </div>
  </div>
@endsection