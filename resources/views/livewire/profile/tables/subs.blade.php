<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}


    <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
      
      <table class="table !mb-0">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Product</th>
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Next Billing Date</th>
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Payment Method</th>
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Amount</th>
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @for($i = 0; $i < 10; $i++)
            <tr>
              <td class="!border-none bg-clip-content !px-0 !text-gray !rounded-tl-2xl !rounded-bl-2xl">
                <div class="!p-3 flex justify-start items-start gap-3 group">
                  <div class="w-20 h-24 rounded overflow-hidden shrink-0">
                    <img class="w-full h-full object-cover" src="http://localhost:9990/storage/images/product_2.jpg" alt="Image">
                  </div>
                  <x-link class="!border-0 group-has-[a]:!text-black text-nowrap">A Guide to Getting to Know North Korea</x-link>
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 !text-gray">
                <div class="!p-3 ">
                  05.28.2025
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 !text-gray">
                <div class="!p-3 ">
                  Visa **** 1234
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 ">
                <div class="!p-3 ">
                  $50.00/month
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 text-nowrap !rounded-tr-2xl !rounded-br-2xl">
                <div class="!p-3 ">
                  <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'cancelsub' })" class="group-has-[a]:hover:!text-black">Cancel Subscription</x-link>
                </div>
              </td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
</div>
