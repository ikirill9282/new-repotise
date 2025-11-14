@foreach ($items as $item)
  <div class="travel-news-item flex flex-col gap-2">
    <x-link href="{{ $item->makeFeedUrl() }}" class="!border-none flex flex-col gap-2">
      <span class="lg:!leading-6">{{ $item->title }}</span>
      <span class="!leading-0 rounded-lg overflow-hidden">
        <img class="max-w-full" src="{{ $item->preview?->image }}" alt="News preview {{ $item->id }}">
      </span>
    </x-link>
  </div>
@endforeach

