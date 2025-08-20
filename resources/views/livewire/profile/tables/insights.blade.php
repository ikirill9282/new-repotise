<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}

    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Content Insights</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Article Views:</div>
            <div class="text-nowrap">10 000</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Engagement Rate:</div>
            <div class="text-nowrap">100%</div>
          </div>
        </div>
      </div>
    </div>

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
          @for($i = 0; $i < 10; $i++)
            <tr>
              <td class="!border-b-gray/15 !py-4 text-nowrap">A Guide to Getting to Know North Korea</td>
              <td class="!border-b-gray/15 !py-4 ">10.10.2025</td>
              <td class="!border-b-gray/15 !py-4 text-nowrap ">1 000 000 000</td>
              <td class="!border-b-gray/15 !py-4">1 000 000 000</td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>

    <div class="text-right">
      <x-btn wire:click.prevent="loadAll" outlined class="!border-active hover:!border-second !w-auto !px-12">
        View All Insights
      </x-btn>
    </div>
</div>
