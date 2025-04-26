<div class="item relative {{ isset($class) ? $class : '' }}">
    @include('site.components.favorite.button', [
        'stroke' => '#FF2C0C',
        'type' => 'author',
        'item_id' => $author->id,
        'class' => 'absolute top-5 right-5 !bg-transparent'
    ])
    <a href="{{ $author->makeProfileUrl() }}" class="profile_commendor">
        <img src="{{ $author->avatar }}" alt="Avatar" class="img_profile rounded-full">
        <div class="right_text">
            <div class="to_block_mob">
                <img src="{{ $author->avatar }}" alt="Avatar" class="img_profile rounded-full">
                <div class="mini_text">
                    {{-- <span>22.01.2025</span> --}}
                    <h3>{{ $author->name }}</h3>
                </div>
            </div>
            <p>{!! $author->description !!}</p>
        </div>
    </a>
</div>
