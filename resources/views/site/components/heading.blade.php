<{{ $variables->get('heading')?->value ?? 'h2' }}>
  {!! $variables->get('header')?->value ?? (isset($title) ? $title : '') !!}
</{{ $variables->get('heading')?->value ?? 'h2' }}>