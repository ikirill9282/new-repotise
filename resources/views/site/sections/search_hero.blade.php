<section class="search_result_home">
  <div class="container !mx-auto">
      <div class="about_block">
          @include('site.components.heading')
          @include('site.components.breadcrumbs')
          <div class="input_group">
              @include('site.components.search')
          </div>
          @if(isset($tags) && !empty($tags))
            <div div class="name_tags">
              @foreach($tags as $tag)
                <a href="{{ url("search?q={$tag['title']}") }}">{{ $tag['title'] }}</a>
                @endforeach
            </div>
          @endif
      </div>
  </div>
</section>