@if(isset($template) && $template == 'main')
  <div class="item h-full flex flex-col items-stretch justify-start !w-full text-[#A4A0A0]">
    <a class="model-preview" href="{{ url($model->makeFeedUrl()) }}">
        <img src="{{ url($model->preview?->image ?? '/assets/img/default-article.jpg') }}"
            alt="model {{ $model->id }}" />
    </a>
    <a class="mb-auto" href="{{ $model->makeFeedUrl() }}">
        <h3>{{ $model->title }}</h3>
    </a>
    <div class="print-content">{{ strip_tags($model->short()) }}</div>
    <div class="name_author">
      <a class="group w-full flex items-center justify-start gap-2" href="{{ $model->author->makeProfileUrl() }}" class="author-link">
        <img class="rounded-full object-cover" src="{{ $model->author->avatar }}"
            alt="Avatar">
        <p class=""><span class="group-hover:!text-black transition">{{ $model->author->name }}</span></p>
      </a>
    </div>
  </div>
@else
  <div class="cards_group">
    <a href="{{ $model->makeFeedUrl() }}">
        <img src="{{ url($model->preview?->image ?? '/assets/img/default-article.jpg') }}"
            alt="model {{ $model->id }}" class="main_img">
    </a>
    <a href="{{ $model->makeFeedUrl() }}">
        <h3>{{ $model->title }}</h3>
    </a>
    <div class="print-content text-[#A4A0A0]">{{ strip_tags($model->short()) }}</div>
    <div class="date">
        <span>{{ \Illuminate\Support\Carbon::parse($model->created_at)->format('d.m.Y') }}</span>
        <div class="name_author">
            <a class="group flex justify-start items-center gap-2"
                href="{{ $model->author->makeProfileUrl() }}">
                <img class="object-cover" src="{{ url($model->author->avatar) }}" alt="Avatar">
                <p class="transition group-hover:!text-black">
                    {{ $model->author->profile }}</p>
            </a>
        </div>
    </div>
  </div>
@endif