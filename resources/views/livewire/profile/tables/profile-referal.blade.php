<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}

    <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
      <table class="min-w-full bg-inherit">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">
              <div class="flex justify-start items-center gap-1.5 group hover:cursor-pointer">
                <div class="">Referred User</div>
                <div class="!text-gray transition group-hover:!text-dark">@include('icons.arrow_down', ['width' => 20, 'height' => 20])</div>
              </div>
            </th>
            <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">
              <div class="flex justify-start items-center gap-1.5 group hover:cursor-pointer">
                <div class="">Referral Date</div>
                <div class="!text-gray transition group-hover:!text-dark">@include('icons.arrow_down', ['width' => 20, 'height' => 20])</div>
              </div>
            </th>
            <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">
              <div class="flex justify-start items-center gap-1.5 group hover:cursor-pointer">
                <div class="">Referral Type</div>
                <div class="!text-gray transition group-hover:!text-dark">@include('icons.arrow_down', ['width' => 20, 'height' => 20])</div>
              </div>
            </th>
            <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">
              <div class="flex justify-start items-center gap-1.5 group hover:cursor-pointer">
                <div class="">Status</div>
                <div class="!text-gray transition group-hover:!text-dark">@include('icons.arrow_down', ['width' => 20, 'height' => 20])</div>
              </div>
            </th>
            <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">
              <div class="flex justify-start items-center gap-1.5 group hover:cursor-pointer">
                <div class="">Promo Codes</div>
                <div class="!text-gray transition group-hover:!text-dark">@include('icons.arrow_down', ['width' => 20, 'height' => 20])</div>
              </div>
            </th>
            <th class="text-nowrap font-normal !border-b-gray/15 !px-4 !pt-4 !pb-6">
              <div class="flex justify-start items-center gap-1.5 group hover:cursor-pointer">
                <div class="">Commission Earned</div>
                <div class="!text-gray transition group-hover:!text-dark">@include('icons.arrow_down', ['width' => 20, 'height' => 20])</div>
              </div>
            </th>
          </tr>
        </thead>
        <tbody class="">
          @for($i = 0; $i < 10; $i++)
            <tr class="first:[&_td:first-child]:!rounded-tl-xl first:[&_td:last-child]:!rounded-tr-xl
                      last:[&_td:first-child]:!rounded-bl-xl last:[&_td:last-child]:!rounded-br-xl"
                      >
              <td class="!bg-white !px-4 !py-4 !border-b-gray/15 text-nowrap">talmaev1</td>
              <td class="!bg-white !px-4 !py-4 !border-b-gray/15 text-nowrap !text-gray">05.28.2025</td>
              <td class="!bg-white !px-4 !py-4 !border-b-gray/15">Buyer - First Purchase</td>
              <td class="!bg-white !px-4 !py-4 !border-b-gray/15 text-nowrap">Registered</td>
              <td class="!bg-white !px-4 !py-4 !border-b-gray/15">
                <div class="flex flex-col copyToClipboard transition hover:cursor-pointer group" data-target="discount{{ $i }}">
                  <div class="text-lg font-bold transition group-hover:!text-active" data-copyId="discount{{ $i }}">NEWYEAR26</div>
                  <div class="flex justify-start items-center gap-2">
                    <span>Discount 15%</span>
                    <span>@include('icons.copy')</span>
                  </div>
                </div>
              </td>
              <td class="!bg-white !px-4 !py-4 !border-b-gray/15 text-nowrap">$50.00</td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
</div>
