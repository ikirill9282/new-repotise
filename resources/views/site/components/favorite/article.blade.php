<div class="item relative {{ isset($class) ? $class : '' }}">
    @include('site.components.favorite.button', [
        'stroke' => '#FF2C0C',
        'type' => 'author',
        'item_id' => $article->author->id,
        'class' => 'absolute top-5 right-5 !bg-transparent'
    ])
    <a href="{{ $article->makeFeedUrl() }}" class="profile_commendor">
        <img src="{{ $article->author->avatar }}" alt="Avatar" class="img_profile rounded-full">
        <div class="right_text">
            <div class="to_block_mob">
                <img src="{{ $article->author->avatar }}" alt="Avatar" class="img_profile rounded-full">
                <div class="mini_text">
                    <h3>{{ $article->title }}</h3>
                </div>
            </div>
            <p>{!! strip_tags($article->short()) !!}</p>
        </div>
    </a>
</div>

