<div class="input_group">
  <form class="search_block" method="GET" action="{{ url('/search') }}">
      <label for="search">
        @include('icons.search')
      </label>
      <input 
        type="search"
        name="q"
        placeholder="{{ print_var('search_text', $variables ?? null) }}"
        @if(request()->has('q'))
          value="{{ request()->get('q') }}"
        @endif
      >
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