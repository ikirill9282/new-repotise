@php
$authors = \App\Models\User::query()
  ->whereHas('roles', fn($query) => $query->whereIn('name', ['creator', 'seller']))
  ->withCount('followers')
  ->orderByDesc('followers_count')
  ->limit(6)
  ->get();
  
if ($authors->count() < 6) {
  while ($authors->count() < 6) {
    $authors = $authors->collect()->merge($authors)->slice(0, 6);
  }
}
@endphp

<section class="authors_blogers">
  <div class="container !mx-auto">
      <div class="about_block">
          @include('site.components.heading')
          <div class="group_authors">
              <div class="swiper mySwiper">
                  <div class="swiper-wrapper">
                      @if(!empty($authors))
                        @foreach ($authors as $author)
                          <div class="swiper-slide">
                              @include('site.components.cards.author', [
                                'model' => $author,
                              ])
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
              <a href="/creators" class="look_more">
								Connect with Creators
              </a>
          </div>
      </div>
  </div>
</section>