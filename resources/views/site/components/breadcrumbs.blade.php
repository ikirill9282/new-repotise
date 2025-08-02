@props([
  'class' => '',
  'listClass' => '',
])

@php
    $breadcrumbs = \App\Helpers\Breadcrumbs::make(
      request()->route(), 
      $current_name ?? null, 
      $exclude ?? null
    );
@endphp

<section class="breadcrumb_block {{ $class }}">
    <div class="container">
        <ol class="breadcrumb {{ $listClass }}">
            @foreach ($breadcrumbs as $name => $breadcrumb)
                @if (array_key_last($breadcrumbs) !== $name)
                  <li class="breadcrumb-item">
                      <a href="{{ $breadcrumb }}">{{ ucfirst($name) }}</a>
                  </li>
                @else
                <li class="breadcrumb-item active" aria-current="page">
                  {{ ucfirst($name) }}
                </li>
                @endif

                @if (array_key_last($breadcrumbs) !== $name)
                    <span>•</span>
                @endif
            @endforeach
        </ol>
    </div>
</section>
