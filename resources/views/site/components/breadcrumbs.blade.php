@php
    $breadcrumbs = \App\Helpers\Breadcrumbs::make(
      request()->route(), 
      $current_name ?? null, 
      $exclude ?? null
    );
@endphp

<section class="breadcrumb_block">
    <div class="container">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $name => $breadcrumb)
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb }}">{{ ucfirst($name) }}</a>
                </li>
                @if (array_key_last($breadcrumbs) !== $name)
                    <span>•</span>
                @endif
            @endforeach
        </ol>
    </div>
</section>
