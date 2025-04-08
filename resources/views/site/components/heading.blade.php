@php
$heading = (isset($variables) && isset($title)) ? $variables->firstWhere(fn($var) => str_contains($var, "{$title}_heading"))?->name : null;
$header = (isset($variables) && isset($title)) ? $variables->firstWhere(fn($var) => str_contains($var, "{$title}_header"))?->name : null;
@endphp

@if(isset($title) && !is_null($heading) && !is_null($header))
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
@endif