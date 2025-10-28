<div class="search_group">
  <form class="search_block relative search-form {{ isset($form_class) ? $form_class : '' }}" {{ isset($form_id) ? " id=$form_id" : '' }} method="GET" action="{{ url('/search') }}">
      @if (!isset($template))
        <label for="search">
          @include('icons.search')
        </label>
        <input 
          type="search"
          name="q"
          class="search-input"
          autocomplete="off"
          data-hits="{{ isset($hits) ? $hits : 'search-hits' }}"
          placeholder="{{ isset($placeholder) ? $placeholder : '' }}"
          @if (isset($attributes) && is_array($attributes))
            @foreach ($attributes as $key => $val)
              {{ $key }}="{{ $val }}"
            @endforeach
          @endif
          @if(request()->has('q'))
            value="{{ request()->get('q') }}"
          @endif
        >
        @include('site.components.hits', ['id' => (isset($hits) ? $hits : 'search-hits')])

        @if(isset($icon) && !$icon)
        @else
          <div class="search_icon">
              <a href="#" class="search-button">
                @include('icons.search', ['stroke' => '#FFFFFF'])
              </a>
          </div>
        @endif
        
      @elseif ($template == 'products')
        <div class="search_top">
          <div class="search_input">
              <label for="search">
                @include('icons.search', ['stroke' => '#FC7361'])
              </label>
              <input 
                name="q"
                class="search-input"
                autocomplete="off"
                data-hits="search-hits"
                placeholder="{{ isset($placeholder) ? $placeholder : '' }}"
                @if (isset($attributes) && is_array($attributes))
                  @foreach ($attributes as $key => $val)
                    {{ $key }}="{{ $val }}"
                  @endforeach
                @endif
                @if(request()->has('q'))
                  value="{{ request()->get('q') }}"
                @endif
              >
          </div>
          <div class="search_icon">
              <a href="#" class="search-button">
                @include('icons.search', ['stroke' => '#FFFFFF'])
              </a>
          </div>
          {{ $buttons ?? '' }}
        </div>
    
        @include('site.components.hits', ['id' => 'search-hits'])

      @elseif($template === 'filters')
        <div class="search_input {{ $wrapClass ?? '' }}">
          <label class="{{ $labelClass ?? '' }}" for="search">
            @include('icons.search')
          </label>
          <input 
            type="search"
            name="q"
            class="search-input {{ $inputClass ?? '' }}"
            autocomplete="off"
            data-hits="{{ isset($hits) ? $hits : 'search-hits' }}"
            placeholder="{{ isset($placeholder) ? $placeholder : '' }}"
            @if (isset($attributes) && is_array($attributes))
              @foreach ($attributes as $key => $val)
                {{ $key }}="{{ $val }}"
              @endforeach
            @endif
          >
          @include('site.components.hits', ['id' => (isset($hits) ? $hits : 'search-hits')])
        </div>
      @endif
  </form>
  <div class="search-error text-sm text-red-500 mt-2 hidden"></div>
  {{ $slot ?? '' }}
</div>
