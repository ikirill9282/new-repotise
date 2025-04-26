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
                              {{-- <div class="cards_group">
                                  <div class="img_products">
                                      <img class="main_img" src="{{ url($author->avatar) }}" alt="Autho {{ $author->getName() }}">
                                      
                                      @include('site.components.favorite.button', [
                                        'stroke' => '#FF2C0C',
                                        'type' => 'author',
                                        'item_id' => $author->id,
                                      ])
                                  </div>
                                  <div class="name">
                                      <p>{{ $author->name }}</p>
                                      <img src="{{ asset('/assets/img/icon_verif.svg') }}" alt="Verify">
                                  </div>
                                  <h3><a class="author-link" href="{{ $author->makeProfileUrl() }}">{{ $author->profile }}</a></h3>
                                  <div class="followers">
                                      <img src="{{ asset('/assets/img/followers.svg') }}" alt="Followers">
                                      <p>{{ $author->followers_count }} Followers</p>
                                  </div>
                              </div> --}}
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
              <a href="{{ print_var('more_link', $variables) }}" class="look_more">
                {{ print_var('more_text', $variables) }}
              </a>
          </div>
      </div>
  </div>
</section>