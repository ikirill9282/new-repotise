<div>
    @if($articles->isEmpty())
      <div class="text-center !py-6">
        <div class="text-lg !leading-6 !mb-6">No articles published yet.<br>Share your travel insights and attract new customers! Create your first article to engage your audience.</div>
        <x-btn href="{{ route('profile.articles.create') }}" class="sm:!w-auto sm:!px-12">Create New Article</x-btn>
      </div>
    @else
      <div class="mb-4">
        <x-form.checkbox 
          label="Choose a product to showcase on your Creator's Page"
          wire:model="all_checked"
        />
      </div>
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
        <div class="flex flex-col items-stretch justify-start gap-4 !pb-3">
          @foreach($articles as $article)
            @php
              $id = \Illuminate\Support\Facades\Crypt::encrypt($article->id);   
            @endphp
            <div class="grid grid-cols-[2rem_10rem_minmax(16rem,100%)_minmax(0,5rem)] md:grid-cols-[2rem_12rem_minmax(0,100%)_minmax(0,16rem)] items-center !gap-4 !mb-4 last:!mb-0 max-w-full">
              <div class="">
                <x-form.checkbox 
                  label=""
                  :id="$id" 
                />
              </div>
              <div class="rounded-lg overflow-hidden h-24 md:h-30">
                <img class="w-full h-full object-cover" src="{{ $article->preview?->image }}" alt="Preview">
              </div>
              <div class="flex justify-start items-start md:items-center flex-col md:flex-row w-full !gap-16 overflow-hidden">
                <div class="w-full md:w-auto md:overflow-hidden">
                  <div class="text-gray truncate mb-2 w-full">{{ $article->title }}</div>
                  <div class="flex justify-start items-center gap-2 text-sm mb-2">
                      @php
                        $displayDate = $article->published_at ?? $article->created_at;
                        $formattedDate = $displayDate
                          ? $displayDate->copy()->timezone(config('app.timezone'))->format('d.m.Y')
                          : '—';
                      @endphp
                      <p class="text-gray bg-light px-2 py-1 rounded-full">{{ $formattedDate }}</p>
                      <p class="text-gray bg-light px-2 py-1 rounded-full">{{ number_format($article->views_total ?? 0) }} Views</p>
                  </div>
                  <div class="">
                    <x-like type="article" count="{{ $article->likes()->count() }}" :id="$article->id"></x-like>
                  </div>
                </div>
              </div>
              <div class="w-full md:w-auto flex flex-col md:flex-row justify-start md:justify-end items-start md:items-center !gap-3 md:!gap-6 !text-sm md:!text-base">
                <x-link>Duplicate</x-link>
                <x-link href="{{ $article->makeEditUrl() }}">Edit</x-link>
                <x-link>Delete</x-link>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
</div>
