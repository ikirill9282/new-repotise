<a href="{{ $author->makeProfileUrl() }}" class="profile_commendor">
  <img src="{{ $author->avatar }}" alt="Avatar" class="img_profile rounded-full">
  <div class="right_text">
      <div class="to_block_mob">
          <img src="{{ $author->avatar }}" alt="Avatar" class="img_profile">
          <div class="mini_text">
              {{-- <span>22.01.2025</span> --}}
              <h3>{{ $author->name }}</h3>
          </div>
      </div>
      <p>{!! $author->description !!}</p>
      {{-- <div class="profile">
          <img src="img/talmaev.svg" alt="">
          <p>@Author</p>
      </div> --}}
  </div>
</a>