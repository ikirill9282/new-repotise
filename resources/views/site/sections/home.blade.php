<section class="home ">
  @include('site.components.parallax', ['class' => 'parallax-home'])
  <div class="container relative z-40 !mx-auto">
      <div class="about_block">
          @include('site.components.heading', ['variables' => $variables])
          <p>{{ print_var('subtitle', $variables) }}</p>
          <div class="bottom_to_calatog">
              <a href="{{ url(print_var('catalog_button_link', $variables)) }}" class="explore_ad">
                {!! print_var('catalog_button_text', $variables) !!}
              </a>
              <a href="{{ url(print_var('catalog_register_link', $variables)) }}" class="become_c">
                {!! print_var('catalog_register_text', $variables) !!}
              </a>
          </div>
      </div>
  </div>
</section>

@push('js')
@endpush