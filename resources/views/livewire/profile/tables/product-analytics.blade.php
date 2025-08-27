<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}
    <x-card>
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
        <div class="flex justify-start items-start xl:items-center flex-col xl:flex-row !gap-4 xl:!gap-8 !mb-10">
          <div class="font-bold text-2xl">Filters</div>
          <div class="flex justify-start items-start sm:items-center !gap-4 2xl:!gap-8 flex-col sm:flex-row">
            <div class="block">
              <label class="text-gray" for="sorting-reviews">Payment Status:</label>
              <select
                id="sorting-reviews"
                class="outline-0 pr-1 hover:cursor-pointer"
                >
                <option value="">All Statuses</option>
                <option value="">All Statuses</option>
                <option value="">All Statuses</option>
              </select>
            </div>
          </div>
        </div>
        <table class="table text-sm md:text-base">
            <thead>
              <tr class="">
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product Name</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Image</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Views</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Units Sold</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Conversion Rate</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Average Rating</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Total Revenue</th>
              </tr>
            </thead>
            <tbody>
              @for($i = 0; $i < 10; $i++)
                <tr>
                  <td class="!border-b-gray/15 !py-4 min-w-2xs">
                    <x-link :border="false">A Guide to Getting to Know North Korea</x-link>
                  </td>
                  <td class="!border-b-gray/15 !py-4 !text-gray">
                    <div class="!w-28 !h-18 rounded overflow-hidden">
                      <img class="object-cover w-full h-full" src="http://localhost:9990/storage/images/product_1.jpg" alt="Product">
                    </div>
                  </td>
                  <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">10 000 000</td>
                  <td class="!border-b-gray/15 !py-4 text-nowrap">10 000 000</td>
                  <td class="!border-b-gray/15 !py-4 ">10 000</td>
                  <td class="!border-b-gray/15 !py-4 ">
                    <div class="flex justify-start items-center h-full">
                      <span class="text-yellow">@include('icons.star')</span>
                      <span>4,9</span>
                    </div>
                  </td>
                  <td class="!border-b-gray/15 !py-4 ">$30 000</td>
                </tr>
              @endfor
            </tbody>
            <tfoot></tfoot>
          </table>
      </div>
    </x-card>
</div>
