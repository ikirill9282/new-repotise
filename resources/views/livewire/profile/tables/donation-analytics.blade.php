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
        <table class="table text-sm md:text-base">
            <thead>
              <tr class="">
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Donor Name</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date & Time</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Transaction ID</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Gross Donation</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Commission</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Net Earnings</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Message</th>
                <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Donation Type</th>
              </tr>
            </thead>
            <tbody>
              @for($i = 0; $i < 10; $i++)
                <tr>
                  <td class="!border-b-gray/15 !py-4 ">@talmaev1</td>
                  <td class="!border-b-gray/15 !py-4 ">05.28.2025</td>
                  <td class="!border-b-gray/15 !py-4 text-nowrap">100000000000</td>
                  <td class="!border-b-gray/15 !py-4 text-nowrap">$3 000</td>
                  <td class="!border-b-gray/15 !py-4 ">$3</td>
                  <td class="!border-b-gray/15 !py-4 ">$3 000</td>
                  <td class="!border-b-gray/15 !py-4 ">A Guide to Getting to Know North Korea</td>
                  <td class="!border-b-gray/15 !py-4 ">Recurring</td>
                </tr>
              @endfor
            </tbody>
            <tfoot></tfoot>
          </table>
      </div>
    </x-card>
</div>
