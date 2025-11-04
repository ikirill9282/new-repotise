<div id="article-analytics">
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}
    <x-card size="sm">
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
        <div class="flex justify-start items-start xl:items-center flex-col xl:flex-row !gap-4 xl:!gap-8 !mb-10">
          <div class="font-bold text-2xl">Filters</div>
          <div class="flex justify-start items-start sm:items-center !gap-4 2xl:!gap-8 flex-col sm:flex-row">
            <div class="block">
              <label class="text-gray" for="sorting-reviews">Product Type:</label>
              <select
                id="sorting-reviews"
                class="outline-0 pr-1 hover:cursor-pointer"
                >
                <option value="">All Types</option>
                <option value="">All Types</option>
                <option value="">All Types</option>
              </select>
            </div>
          </div>
        </div>
        @if($rows->isEmpty())
          <div class="py-6 text-center text-gray">No article analytics available.</div>
        @else
          <table class="table text-sm md:text-base">
              <thead>
                <tr class="">
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Article Title</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Image</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Views</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Likes</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Comments</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Engagement Rate</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Avg. Read Time</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rows as $row)
                  @php
                    $article = $row['article'];
                    $preview = $article?->preview?->image;
                  @endphp
                  <tr>
                    <td class="!border-b-gray/15 !py-4 min-w-2xs">
                      <x-link :border="false" :href="$article->makeFeedUrl()">{{ $article->title }}</x-link>
                    </td>
                    <td class="!border-b-gray/15 !py-4 !text-gray">
                      <div class="!w-28 !h-18 rounded overflow-hidden bg-light flex items-center justify-center">
                        @if($preview)
                          <img class="object-cover w-full h-full" src="{{ $preview }}" alt="{{ $article->title }}">
                        @else
                          <span class="text-sm text-gray">No image</span>
                        @endif
                      </div>
                    </td>
                    <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">{{ number_format($row['views']) }}</td>
                    <td class="!border-b-gray/15 !py-4 text-nowrap">{{ number_format($row['likes']) }}</td>
                    <td class="!border-b-gray/15 !py-4 ">{{ number_format($row['comments']) }}</td>
                    <td class="!border-b-gray/15 !py-4 ">{{ number_format($row['engagement_rate'], 2) }}%</td>
                    <td class="!border-b-gray/15 !py-4 ">{{ $row['avg_read_time'] ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot></tfoot>
            </table>
        @endif
      </div>
    </x-card>
</div>
