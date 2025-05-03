@php
$params = request()->route()->parameters();
$location = array_key_exists('country', $params) ? \App\Models\Location::firstWhere('slug', $params['country']) : null;
@endphp

<section class="home_discover relative">
  @include('site.components.parallax', [
    'class' => 'parallax-products',
    'attributes' => [
      'data-url' => ($location && $location->hasPoster()) ? $location->poster : '',
      'style' => ($location && $location->hasPoster()) ? "background-image: url($location->poster)" : '',
    ]
  ])
  <div class="container relative z-10">
      <div class="about_block">
          @include('site.components.heading')
          @include('site.components.breadcrumbs', [
            'current_name' => $location?->title,
          ])
      </div>
  </div>
</section>