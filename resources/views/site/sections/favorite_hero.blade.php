<section class="favorites_home relative">
  @include('site.components.parallax', ['class' => 'parallax-favorite'])
  <div class="container relative z-10">
      <div class="about_block">
          @include('site.components.heading', ['variables' => $variables])
          @include('site.components.breadcrumbs')
      </div>
  </div>
</section>