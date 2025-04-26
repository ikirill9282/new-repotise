<div class="cards_group relative flex flex-col justify-start items-start">
  <div class="img_products item !max-w-none !w-full">
      <img class="main_img !w-full" src="{{ url($model->avatar) }}" alt="Autho {{ $model->getName() }}">
      
      @include('site.components.favorite.button', [
        'stroke' => '#FF2C0C',
        'type' => 'author',
        'item_id' => $model->id,
        'class' => 'transition absolute top-3 right-3 p-2 rounded bg-[rgba(249,_249,_249,_0.5)] lg:hover:bg-white fill-red',
      ])
  </div>
  <div class="name flex gap-2 my-1">
      <p>{{ $model->name }}</p>
      <img src="{{ asset('/assets/img/icon_verif.svg') }}" alt="Verify">
  </div>
  <h3 class="!text-sm mb-1"><a class="author-link" href="{{ $model->makeProfileUrl() }}">{{ $model->profile }}</a></h3>
  <div class="followers flex text-[#A4A0A0] text-sm gap-2">
      <img src="{{ asset('/assets/img/followers.svg') }}" alt="Followers">
      <p>{{ $model->followers_count }} Followers</p>
  </div>
</div>