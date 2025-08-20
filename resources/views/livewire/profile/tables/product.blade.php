<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}

    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Product</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Product Page Views:</div>
            <div class="text-nowrap">10 000</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Average Rating:</div>
            <div class="text-nowrap">5</div>
          </div>
        </div>
      </div>
    </div>

    <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
      <div class="font-bold text-lg px-1 mb-4">Top Products</div>
      <table class="table">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product Name</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Image</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Units Sold</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Total Revenue</th>
          </tr>
        </thead>
        <tbody>
          @for($i = 0; $i < 10; $i++)
            <tr>
              <td class="!border-b-gray/15 !py-4 text-nowrap">A Guide to Getting to Know North Korea</td>
              <td class="!border-b-gray/15 !py-4 !text-gray">
                <div class="w-36 h-18 rounded overflow-hidden">
                  <img class="w-full h-full object-cover" src="http://localhost:9990/storage/images/product_2.jpg" alt="Image">
                </div>
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">1 000</td>
              <td class="!border-b-gray/15 !py-4">$50.00</td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>

    <div class="text-right">
      <x-btn wire:click.prevent="loadAll" outlined class="!border-active hover:!border-second !w-auto !px-12">
        View All Products Analytics
      </x-btn>
    </div>
</div>
