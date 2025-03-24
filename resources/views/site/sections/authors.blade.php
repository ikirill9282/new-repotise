@php
$authors = $variables->get('author_ids')?->value ?? [];
if (!empty($authors)) {
  $authors = \App\Models\User::whereIn('id', $authors)->withCount('followers')->get();
  while ($authors->count() < 6) {
    $authors = $authors->collect()->merge($authors)->slice(0, 6);
  }
}
@endphp

<section class="authors_blogers">
  <div class="container">
      <div class="about_block">
          @include('site.components.heading', ['variables' => $variables])
          <div class="group_authors">
              <div class="swiper mySwiper">
                  <div class="swiper-wrapper">
                    @if(!empty($authors))
                      @foreach ($authors as $author)
                        <div class="swiper-slide">
                            <div class="cards_group">
                                <div class="img_products">
                                    <img src="{{ url($author->avatar) }}" alt="" class="main_img">
                                    <a href="{{ url('/user/favorite/add/author') }}" class="span_buy">
                                        @include('icons.favorite', ['stroke' => '#FF2C0C'])
                                      </a>
                                </div>
                                <div class="name">
                                    <p>{{ $author->getName() }}</p>
                                    <img src="{{ asset('/assets/img/icon_verif.svg') }}" alt="Verified">
                                </div>
                                <h3><a href="{{ $author->makeProfileUrl() }}">{{ $author->profile() }}</a></h3>
                                <div class="followers">
                                    <img src="{{ asset('/assets/img/followers.svg') }}" alt="Followers">
                                    <p>{{ $author->followers_count }} Followers</p>
                                </div>
                            </div>
                        </div>
                      @endforeach
                    @endif
                  </div>
                  <div class="swiper-button-next">
                    @include('icons.analog_arrow_next')
                  </div>
                  <div class="swiper-button-prev">
                    @include('icons.analog_arrow_prev')
                  </div>
              </div>
              <a href="{{ $variables->get('more_link')?->value ?? '#' }}" class="look_more">
                {{ $variables->get('more_text')?->value ?? '' }}
              </a>
          </div>
      </div>
  </div>
</section>