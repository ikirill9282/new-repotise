@php
    $questions = \App\Models\FAQ::where('type', 'question')->with('answer')->get()->groupBy('group');
@endphp


<section class="home_help relative">
    <div class="parallax parallax-help"></div>
    <div class="container !mx-auto relative z-40">
        <div class="about_block">
            @include('site.components.heading', ['variables' => $variables])
            @include('site.components.breadcrumbs')
        </div>
    </div>
</section>

<section class="general_questions">
    <div class="container !mx-auto">
        <div class="about_block">
            <div class="questions_group">
                <div class="accordion" id="accordionExample">
                    @foreach ($questions as $group => $questions_group)
                        @include('site.components.heading', ['title' => $group])
                        @foreach ($questions_group as $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-{{ $item->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $item->id }}" aria-expanded="true"
                                        aria-controls="collapse-{{ $item->id }}">
                                        {!! $item->text !!}
                                    </button>
                                </h2>
                                <div id="collapse-{{ $item->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample" aria-labelledby="heading-{{ $item->id }}">
                                    <div class="accordion-body">
                                        <div class="text_answers">
                                            {!! $item->answer->text !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            @include('site.components.last_news')
        </div>
    </div>
</section>

@push('js')
<script>
  const init_slider = () => {
    if ($(window).outerWidth() < 768) {
      return new Swiper('#last_news_swiper', {
        slidesPerView: 1.4,
        spaceBetween: 20,
        enabled: true,
        breakpoints: {
          400: {
            slidesPerView: 1.6,
          },
          500: {
            slidesPerView: 1.9,
          },
          768: {
            enabled: false,
            slidesPerView: 4,
          },
          1200: {
            slidesPerView: 5,
          },
        }
      });
    }
    return null;
  }

  let slider = init_slider();
  let resized = false;
  let disabled = false;
  $(window).on('resize', function() {
    slider = init_slider();
  });
</script>
@endpush