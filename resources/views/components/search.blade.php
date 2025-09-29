@props([
  'hits' => null,
  'formClass' => '',
  'formId' => '',
  'button' => true,
  'action' => url('/search'),
])

<div class="search_group flex flex-col">
  <form class="search_block relative search-form {{ $formClass }}" id="{{ $formId }}" method="GET" action="{{ $action }}">
    <div class="search-wrap relative">
      <label for="search">
        @include('icons.search', ['width' => 20, 'height' => 20])
      </label>
      <input 
        type="search"
        name="q"
        class="search-input"
        autocomplete="off"
        data-hits="{{ $hits ?? 'search-hits' }}"
        value="{{ request()->get('q') ?? '' }}"
        {{ $attributes }}
      >
      @include('site.components.hits', ['id' => $hits ?? 'search-hits'])
    </div>

    @if($button)
      <x-btn href="#" class="!w-auto !p-3 !rounded-lg search-button hover:!bg-second">Search</x-btn>
    @endif
  </form>
  {{ $slot }}
</div>
