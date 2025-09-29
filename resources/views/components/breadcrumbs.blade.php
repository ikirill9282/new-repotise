@props([
  'breadcrumbs' => [],
  'last' => 'last:!text-black',
  'listClass' => '',
])

<div class="text-gray {{ $attributes->get('class') }}">
  <div class="flex items-center justify-start flex-wrap xs:flex-nowrap !gap-2 xs:!gap-3 text-sm sm:text-base text-nowrap {{ $listClass }}">
      @foreach ($breadcrumbs as $name => $breadcrumb)
          <div class="{{ $last }}">
            <x-link class="!text-inherit !border-none" href="{{ $breadcrumb }}">{{ ucfirst($name) }}</x-link>
          </div>

          @if (array_key_last($breadcrumbs) !== $name)
              <span>•</span>
          @endif
      @endforeach
  </div>
</div>