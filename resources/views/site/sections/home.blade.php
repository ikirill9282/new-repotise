
<section class="home ">
  @include('site.components.parallax', ['class' => 'parallax-home'])
  <div class="container relative z-40 !mx-auto">
      <div class="about_block">
          @include('site.components.heading', ['variables' => $variables])

          <p>{{ print_var('page_subtitle', $variables) }}</p>
          <div class="bottom_to_calatog">
              <a href="{{ url(print_var('page_catalog_button_link', $variables)) }}" class="explore_ad">
                {!! print_var('page_catalog_button_text', $variables) !!}
              </a>
              @if(auth()->check())
                <a href="{{ auth()->user()->verify ? url('/profile/article/cleate') : auth()->user()->makeProfileVerificationUrl() }}" class="become_c">
                  {!! print_var('page_catalog_register_text', $variables) !!}
                </a>
              @else
                <a href="#" class="become_c open_auth">
                  {!! print_var('page_catalog_register_text', $variables) !!}
                </a>
              @endif
          </div>
      </div>
  </div>
</section>

@push('js')
@endpush