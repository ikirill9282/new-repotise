<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}

    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Recent Reviews</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          
        </div>
      </div>
    </div>

    <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
      <table class="table">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Username</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Rating</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @for($i = 0; $i < 10; $i++)
            <tr>
              <td class="!border-b-gray/15 !py-4 text-nowrap">talmaev1</td>
              <td class="!border-b-gray/15 !py-4">
                <x-stars active="3" />
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">10.10.2025</td>
              <td class="!border-b-gray/15 !py-4">
                <x-link>Reply</x-link>
              </td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>

    <div class="text-right">
      <x-btn wire:click.prevent="loadAll" outlined class="!border-active hover:!border-second !w-auto !px-12">
        View All Donations
      </x-btn>
    </div>
</div>
