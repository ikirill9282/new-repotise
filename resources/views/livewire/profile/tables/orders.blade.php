<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}


    <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
      <table class="table !mb-0 ">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Date</th>
            <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Order</th>
            <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Product</th>
            <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Actions</th>
            <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Price</th>
          </tr>
        </thead>
        <tbody>
          @for($i = 0; $i < 10; $i++)
            <tr class="">
              <td class="bg-clip-content !px-0 !text-gray !border-light !rounded-tl-2xl !rounded-bl-2xl">
                <div class="!p-3 rounded-tl-lg rounded-bl-lg ">05.28.2025</div>
              </td>
              <td class="bg-clip-content !px-0 !text-gray !border-light">
                <div class="!p-3 ">#123454567</div>
              </td>
              <td class="bg-clip-content !px-0 !text-gray !border-light">
                <div class="!p-3 flex justify-start items-start gap-3 group ">
                  <div class="w-20 h-24 rounded overflow-hidden shrink-0">
                    <img class="w-full h-full object-cover" src="http://localhost:9990/storage/images/product_2.jpg" alt="Image">
                  </div>
                  <x-link class="!border-0 group-has-[a]:!text-black text-nowrap">A Guide to Getting to Know North Korea</x-link>
                </div>
              </td>
              <td class="bg-clip-content !px-0 text-nowrap !border-light">
                <div class="!p-3 flex items-start justify-start gap-4 group ">
                  <div class="flex">
                    <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'product' })">View & Download</x-link>
                  </div>
                  <div class="flex flex-col items-start justify-start gap-2">
                    <x-link class="group-has-[a]:hover:!text-black group-has-[a]:hover:!border-black">Leave Review</x-link>
                    <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'refund' })" class="group-has-[a]:hover:!text-black group-has-[a]:hover:!border-black">Refund</x-link>
                  </div>
                </div>
              </td>
              <td class="bg-clip-content !px-0 !border-light !rounded-tr-2xl !rounded-br-2xl">
                <div class="!p-3 ">$50.00</div>
              </td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
</div>
