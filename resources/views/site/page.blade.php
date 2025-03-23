@extends('layouts.site')

@section('content')
    @if (isset($page) && isset($page->sections) && !empty($page->sections))
        @foreach ($page->sections as $section)
            @php
              $variables = $section->variables->keyBy('name');
            @endphp

            @if ($section->type === 'wire')
                @livewire($section->component, ['variables' => $variables])
            @else
                @include("site.sections.$section->component", [
                    'variables' => $variables,
                ])
            @endif
        @endforeach
    @endif
@endsection
