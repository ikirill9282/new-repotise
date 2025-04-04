@push('css')
  <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
@endpush


<section class="container py-4 custom-section">
  @foreach ($variables->sortBy('id') as $variable)
    {!! $variable->value !!}
  @endforeach
</section>