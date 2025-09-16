@props([
  'breadcrumbs' => [],
])

<div class="text-gray {{ $attributes->get('class') }}">
  <div class="flex items-center justify-start !gap-3 {{ $listClass ?? '' }}">
      @foreach ($breadcrumbs as $name => $breadcrumb)
          <div class="last:!text-black">
            <x-link class="!text-inherit !border-none" href="{{ $breadcrumb }}">{{ ucfirst($name) }}</x-link>
          </div>

          @if (array_key_last($breadcrumbs) !== $name)
              <span>•</span>
          @endif
      @endforeach
  </div>
</div>