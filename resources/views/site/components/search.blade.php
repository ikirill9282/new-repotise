<div class="input_group">
  <form class="search_block relative" method="GET" action="{{ url('/search') }}">
      <label for="search">
        @include('icons.search')
      </label>
      <input 
        type="search"
        name="q"
        class="search-input"
        autocomplete="off"
        data-hits="search-hits"
        placeholder="{{ print_var('search_placeholder', $variables ?? null) }}"
        @if(request()->has('q'))
          value="{{ request()->get('q') }}"
        @endif
      >
      @include('site.components.hits', ['id' => 'search-hits'])
  </form>
  @if(isset($icon) && !$icon)
  @else
    <div class="search_icon">
        <a href="#">
          @include('icons.search', ['stroke' => '#FFFFFF'])
        </a>
    </div>
  @endif
</div>