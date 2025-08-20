<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}

    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Refunds Summary</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Total Refunds:</div>
            <div class="text-nowrap">10 000</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Refund Rate:</div>
            <div class="text-nowrap">10 000</div>
          </div>
        </div>
      </div>
    </div>

    <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
      <div class="font-bold text-lg px-1 mb-4">Recent Refunds</div>
      <table class="table">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date & Time</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Order #</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product Name</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">
              <div class="relative inline-block !pr-6">
                Act on Request
                <x-tooltip message="tooltip" />
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          @for($i = 0; $i < 10; $i++)
            <tr>
              <td class="!border-b-gray/15 !py-4 text-nowrap">10.10.2025 10:00</td>
              <td class="!border-b-gray/15 !py-4">Donor Name</td>
              <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">$300</td>
              <td class="!border-b-gray/15 !py-4">
                <x-link>20/30 Act on Request</x-link>
              </td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>

    <div class="text-right">
      <x-btn wire:click.prevent="loadAll" outlined class="!border-active hover:!border-second !w-auto !px-12">
        View All Refunds
      </x-btn>
    </div>
</div>
