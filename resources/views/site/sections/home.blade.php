<section class="home">
  <div class="container">
      <div class="about_block">
          @include('site.components.heading', ['variables' => $variables])
          <p>{{ $variables->get('subtitle')?->value }}</p>
          <div class="">
            <a href="{{ url($variables->get('catalog_button_link')?->value ?? '') }}">
              {!! $variables->get('catalog_button_text')?->value !!}
            </a>
            <a href="{{ url($variables->get('catalog_register_link')?->value ?? '') }}">
              {!! $variables->get('catalog_register_text')?->value !!}
            </a>
          </div>
      </div>
  </div>
</section>