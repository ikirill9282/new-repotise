@php
// $heading = (isset($variables) && isset($title)) ? $variables->firstWhere(fn($var) => str_contains($var, "{$title}_heading"))?->name : null;
// $header = (isset($variables) && isset($title)) ? $variables->firstWhere(fn($var) => str_contains($var, "{$title}_header"))?->name : null;
$heading = (isset($variables)) ? $variables->where(fn($item) => str_contains($item->name, 'heading'))->first()?->value : null;
$header = (isset($variables)) ? $variables->where(fn($item) => str_contains($item->name, 'header'))->first()?->value : null;

@endphp

<{{ $heading ?? 'h2' }}>
  @if(isset($header_text) && !empty($header_text)) 
    {!! $header_text . (isset($append) ? " $append" : '') !!}
  @else
    {!! ($header ?? ""). (isset($append) ? " $append" : '') !!}
  @endif
</{{ $heading ?? 'h2' }}>

{{-- @if(isset($title) && !is_null($heading))
  <{{ print_var($heading, $variables) }}>
        
    @if(isset($header_text) && !empty($header_text)) 
      {!! $header_text . (isset($append) ? " $append" : '') !!}
    @else
      {!! print_var($header, $variables) . (isset($append) ? " $append" : '') !!}
    @endif
    
  </{{ print_var($heading, $variables) }}>
@elseif (isset($variables))
  <{{ print_var('heading', $variables) }}>

    @if(isset($header_text) && !empty($header_text)) 
      {!! $header_text . (isset($append) ? " $append" : '') !!}
    @else
      {!! print_var('header', $variables) . (isset($append) ? " $append" : '') !!}
    @endif
  
    </{{ print_var('heading', $variables) }}>
@endif --}}