<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}


    <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
      
      <table class="table !mb-0">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Order</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Actions</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Price</th>
          </tr>
        </thead>
        <tbody>
          @for($i = 0; $i < 10; $i++)
            <tr>
              <td class="!text-gray">05.28.2025</td>
              <td class="!text-gray">#123454567</td>
              <td class="!border-b-gray/15 !text-gray">
                <div class="flex justify-start items-start gap-3 group">
                  <div class="w-20 h-24 rounded overflow-hidden">
                    <img class="w-full h-full object-cover" src="http://localhost:9990/storage/images/product_2.jpg" alt="Image">
                  </div>
                  <x-link class="!border-0 group-has-[a]:!text-black">A Guide to Getting to Know North Korea</x-link>
                </div>
              </td>
              <td class="!border-b-gray/15 text-nowrap">
                <div class="flex items-start justify-start gap-4 group">
                  <div class="flex">
                    <x-link class="group-has-[a]:!text-active">View & Download</x-link>
                  </div>
                  <div class="flex flex-col items-start justify-start gap-2">
                    <x-link class="group-has-[a]:hover:!text-black">Leave Review</x-link>
                    <x-link class="group-has-[a]:hover:!text-black">Refund</x-link>
                  </div>
                </div>
              </td>
              <td class="!border-b-gray/15 !py-4">$50.00</td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
</div>
