<div>
    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Content Insights</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Article Views:</div>
            <div class="text-nowrap">{{ number_format($summary['views']) }}</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Engagement Rate:</div>
            <div class="text-nowrap">
              {{ $summary['engagement_rate'] > 0 ? number_format($summary['engagement_rate'], 2).'%' : '—' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($rows->isEmpty())
      <div class="text-lg text-center text-gray">You haven't published any insights yet.</div>
    @else
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
        <div class="font-bold text-lg px-1 mb-4">Recent Articles</div>
        <table class="table">
          <thead>
            <tr class="">
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Article Title</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Publication Date</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Views</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Likes</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $article)
              @php
                $date = $article->published_at ?? $article->created_at;
                $formattedDate = $date
                  ? $date->copy()->timezone(config('app.timezone'))->format('m.d.Y')
                  : '—';
              @endphp
              <tr>
                <td class="!border-b-gray/15 !py-4 text-nowrap">
                  <x-link :href="$article->makeFeedUrl()" class="!border-0">
                    {{ $article->title }}
                  </x-link>
                </td>
                <td class="!border-b-gray/15 !py-4">
                  {{ $formattedDate }}
                </td>
                <td class="!border-b-gray/15 !py-4 text-nowrap">
                  {{ number_format((int) $article->views) }}
                </td>
                <td class="!border-b-gray/15 !py-4">
                  {{ number_format((int) ($article->likes_count ?? 0)) }}
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot></tfoot>
        </table>
      </div>

      @if($hasMore)
        <div class="text-right">
          <x-btn wire:click.prevent="loadAll" outlined class="!border-active hover:!border-second !w-auto !px-12">
            View All Insights
          </x-btn>
        </div>
      @endif
    @endif
</div>
