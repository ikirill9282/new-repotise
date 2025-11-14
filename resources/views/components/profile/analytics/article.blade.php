@props(['stats' => []])

<div class="!p-2 sm:!p-4 lg:!p-8 bg-light basis-1/2 rounded flex flex-col justify-start items-start gap-2">
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Average Article Views:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ number_format($stats['average_views'] ?? 0, 1) }}</span>
      <x-tooltip message="Average number of views each article received during the selected time period.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Article Likes:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ number_format($stats['likes'] ?? 0) }}</span>
      <x-tooltip message="Total likes your articles received during the selected time period.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-center gap-1.5 flex-wrap">
    <div class="text-gray">Article Comments:</div>
    <div class="text-nowrap relative !pr-6">
      <span>{{ number_format($stats['comments'] ?? 0) }}</span>
      <x-tooltip message="Total comments posted on your articles during the selected time period.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
  <div class="flex justify-start items-start gap-1.5 flex-wrap md:!flex-nowrap">
    <div class="text-gray md:text-nowrap">Top Popular Article:</div>
    <div class=" relative !pr-6 group">
      @php $topArticle = $stats['top_article'] ?? null; @endphp
      @if($topArticle)
        <x-link class="group-has-[a]:!text-active" :border="false" href="{{ $topArticle->makeFeedUrl() }}">{{ $topArticle->title }}</x-link>
      @else
        <span class="text-gray">—</span>
      @endif
      <x-tooltip class="!top-1 !translate-y-[0]" message="Article with the highest engagement (views, likes, comments) during the selected time period.">@include('icons.shield')</x-tooltip>
    </div>
  </div>
</div>
