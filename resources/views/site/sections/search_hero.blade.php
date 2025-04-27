<section class="search_result_home relative">
  @include('site.components.parallax', ['class' => 'parallax-search'])
  <div class="container !mx-auto relative z-50">
      <div class="about_block">
          @include('site.components.heading')
          @include('site.components.breadcrumbs')
          <div class="input_group flex-wrap !justify-start">
              @include('site.components.search', ['placeholder' => print_var('search_placeholder', $variables ?? null)])

              @if(isset($tags) && !empty($tags))
                <div div class="name_tags">
                  @foreach($tags as $tag)
                    <a href="{{ url("search?q={$tag['title']}") }}">{{ $tag['title'] }}</a>
                    @endforeach
                </div>
              @endif
          </div>
      </div>
  </div>
</section>