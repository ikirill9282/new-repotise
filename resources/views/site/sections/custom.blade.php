<section class="container !mx-auto py-4 custom-section {{ $page->slug }}">
  @foreach ($variables->sortBy('id') as $variable)
    {!! $variable->value !!}
  @endforeach
</section>