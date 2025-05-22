@extends('layouts.site')

@section('content')
    @if (isset($page) && isset($page->sections) && !empty($page->sections))
        @foreach ($page->sections as $section)
            @php
                // dd(auth()->user());
                if (isset($section->variables)) {
                    $variables = $section->variables->keyBy('name');
                } else {
                    $variables = collect([]);
                }
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
    <div class="hidden !w-[250px] whitespace-normal"></div>
@endsection
