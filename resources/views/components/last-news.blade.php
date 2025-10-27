@props([
  'items' => \App\Models\Article::getLastNews(),
])

@php
  // Подсчёт новостей
  $totalNews = \App\Models\Article::whereHas('author', fn($query) => $query->where('id', 0))->count();
@endphp

<div class="">
  <div class="swiper" id="last_news_swiper">
      <div class="swiper-wrapper">
          @foreach ($items as $item)
              <div class="swiper-slide max-w-[180px] sm:max-w-[250px]">
                  <div class="text-sm lg:text-base">
                      <x-link href="{{ $item->makeFeedUrl() }}" class="!border-none flex flex-col gap-2">
                        <span class="lg:!leading-6">{{ $item->title }}</span>
                        <span class="!leading-0 rounded-lg overflow-hidden"><img class="max-w-full" src="{{ $item->preview?->image }}" alt="News preview {{ $item->id }}"></span>
                      </x-link>
                  </div>
              </div>
          @endforeach
      </div>
  </div>
  
  <!-- Вывод счётчика под слайдером -->
  <p class="text-gray-500 text-sm mt-2">Total news: {{ $totalNews }}</p>
</div>
