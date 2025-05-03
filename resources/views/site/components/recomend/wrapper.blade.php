@php
$max = isset($max) ? $max : 12;
while($models->count() < $max) {
  $models = $models->collect()->merge($models)->slice(0, $max);
}
@endphp
<div class="favorite_products">
  <h2>Recommended for you</h2>
  <div class="products_item">
      <div class="swiper mySwiperRecomend">
          <div class="swiper-wrapper">
              @foreach($models as $model)
              <div class="swiper-slide">                
                  @include("site.components.cards.$card", [
                    'model' => $model,
                    'variables' => $variables,
                  ])
              </div>
              @endforeach
          </div>
          <div class="swiper-button-next"><svg xmlns="http://www.w3.org/2000/svg"
                  width="40" height="40" viewBox="0 0 40 40" fill="none">
                  <g opacity="0.6">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M20.4173 4.5835C11.904 4.5835 5.00065 11.4852 5.00065 20.0002C5.00065 28.5135 11.904 35.4168 20.4173 35.4168C28.9306 35.4168 35.834 28.5135 35.834 20.0002C35.834 11.4852 28.9307 4.5835 20.4173 4.5835Z"
                          fill="#212121" stroke="#212121" stroke-width="1.5"
                          stroke-linecap="square" />
                      <path d="M17 14L22.81 19.785L17 25.57" stroke="white"
                          stroke-width="1.5" stroke-linecap="round" />
                  </g>
              </svg></div>
          <div class="swiper-button-prev"><svg xmlns="http://www.w3.org/2000/svg"
                  width="40" height="40" viewBox="0 0 40 40" fill="none">
                  <g opacity="0.6">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M20.4173 4.5835C11.904 4.5835 5.00065 11.4852 5.00065 20.0002C5.00065 28.5135 11.904 35.4168 20.4173 35.4168C28.9306 35.4168 35.834 28.5135 35.834 20.0002C35.834 11.4852 28.9307 4.5835 20.4173 4.5835Z"
                          fill="#212121" stroke="#212121" stroke-width="1.5"
                          stroke-linecap="square" />
                      <path d="M17 14L22.81 19.785L17 25.57" stroke="white"
                          stroke-width="1.5" stroke-linecap="round" />
                  </g>
              </svg></div>
      </div>
  </div>
</div>