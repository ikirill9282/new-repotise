<div id="donation-analytics">
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
          <div class="py-6 text-center text-gray">No donations recorded for this period.</div>
        @else
          <table class="table text-sm md:text-[15px]">
              <thead>
                <tr class="">
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Donor Name</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date & Time</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Transaction ID</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Gross Donation</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Commission</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Net Earnings</th>
                  <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Donation Type</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rows as $row)
                  @php
                    $donor = $row['donor'];
                  @endphp
                  <tr>
                    <td class="!border-b-gray/15 !py-4 ">{{ $donor?->username ?? $donor?->name ?? 'Anonymous' }}</td>
                    <td class="!border-b-gray/15 !py-4 ">{{ $row['date']?->format('m.d.Y H:i') ?? '—' }}</td>
                    <td class="!border-b-gray/15 !py-4 text-nowrap">{{ $row['transaction_id'] }}</td>
                    <td class="!border-b-gray/15 !py-4 text-nowrap">{{ currency($row['gross']) }}</td>
                    <td class="!border-b-gray/15 !py-4 ">{{ currency($row['commission']) }}</td>
                    <td class="!border-b-gray/15 !py-4 ">{{ currency($row['net']) }}</td>
                    <td class="!border-b-gray/15 !py-4 ">{{ $row['type'] }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot></tfoot>
          </table>
        @endif
      </div>
    </x-card>
</div>
