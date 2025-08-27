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
            <div class="block">
              <label class="text-gray" for="sorting-reviews">Product:</label>
              <select 
                id="sorting-reviews"
                class="outline-0 pr-1 hover:cursor-pointer"
                >
                <option value="">All Products</option>
                <option value="">All Products</option>
                <option value="">All Products</option>
              </select>
            </div>
            <div class="block">
              <label class="text-gray" for="sorting-reviews">Order Type:</label>
              <select
                id="sorting-reviews"
                class="outline-0 pr-1 hover:cursor-pointer"
                >
                <option value="">All Order Types</option>
                <option value="">All Order Types</option>
                <option value="">All Order Types</option>
              </select>
            </div>
          </div>
        </div>
        <table class="table">
            <thead>
              <tr class="">
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Order #</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product Name</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Status</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Gross Revenue</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Commissions</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Net Earnings</th>
              </tr>
            </thead>
            <tbody>
              @for($i = 0; $i < 10; $i++)
                <tr>
                  <td class="!border-b-gray/15 !py-4 !text-gray">05.28.2026</td>
                  <td class="!border-b-gray/15 !py-4 !text-gray">#J4RW45Z</td>
                  <td class="!border-b-gray/15 !py-4 text-nowrap">A Guide to Getting to Know North Korea</td>
                  <td class="!border-b-gray/15 !py-4 text-nowrap">Refund Processing</td>
                  <td class="!border-b-gray/15 !py-4 text-center">$300</td>
                  <td class="!border-b-gray/15 !py-4 text-center">$300</td>
                  <td class="!border-b-gray/15 !py-4 text-center">$300</td>
                </tr>
              @endfor
            </tbody>
            <tfoot></tfoot>
          </table>
      </div>
    </x-card>
</div>
