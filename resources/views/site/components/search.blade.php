<div class="input_group">
  <div class="search_block">
      <label for="search">
        @include('icons.search')
      </label>
      <input type="search" placeholder="{{ print_var('search_text', $variables ?? null) }}">
  </div>
  <div class="search_icon">
      <a href="#">
        @include('icons.search', ['stroke' => '#FFFFFF'])
      </a>
  </div>
</div>