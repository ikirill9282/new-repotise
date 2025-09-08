<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}


    <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
      
      <table class="table !mb-0">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Next Billing Date</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Payment Method</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Amount</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @for($i = 0; $i < 10; $i++)
            <tr>
              <td class="!border-b-gray/15 !text-gray">
                <div class="flex justify-start items-start gap-3 group">
                  <div class="w-20 h-24 rounded overflow-hidden">
                    <img class="w-full h-full object-cover" src="http://localhost:9990/storage/images/product_2.jpg" alt="Image">
                  </div>
                  <x-link class="!border-0 group-has-[a]:!text-black">A Guide to Getting to Know North Korea</x-link>
                </div>
              </td>
              <td class="!text-gray">05.28.2025</td>
              <td class="!text-gray">Visa **** 1234</td>
              <td>$50.00/month</td>
              <td class="!border-b-gray/15 text-nowrap">
                <x-link class="group-has-[a]:hover:!text-black">Cancel Subscription</x-link>
              </td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
</div>
