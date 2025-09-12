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
            <tr class="[&_td:first-child]:!rounded-tl-xl [&_td:last-child]:!rounded-tr-xl
                      [&_td:first-child]:!rounded-bl-xl  [&_td:last-child]:!rounded-br-xl border-y-[20px]"
                      >
              <td class="!border-none !bg-white !bg-clip-content !py-1 text-nowrap !rounded-tl-2xl !rounded-bl-2xl">
                <div class="!px-4 !py-6">
                  talmaev1
                </div>
              </td>
              <td class="!border-none !bg-white !bg-clip-content !py-1  text-nowrap !text-gray">
                <div class="!px-4 !py-6">
                  05.28.2025
                </div>
              </td>
              <td class="!border-none !bg-white !bg-clip-content !py-1 ">
                <div class="!px-4 !py-6">
                  Buyer - First Purchase
                </div>
              </td>
              <td class="!border-none !bg-white !bg-clip-content !py-1  text-nowrap">
                <div class="!px-4 !py-6">
                  Registered
                </div>
              </td>
              <td class="!border-none !bg-white !bg-clip-content !py-1 ">
                <div class="!px-4 !py-6 flex flex-col copyToClipboard transition hover:cursor-pointer group" data-target="discount{{ $i }}">
                  <div class="text-lg font-bold transition group-hover:!text-active" data-copyId="discount{{ $i }}">NEWYEAR26</div>
                  <div class="flex justify-start items-center gap-2">
                    <span>Discount 15%</span>
                    <span>@include('icons.copy')</span>
                  </div>
                </div>
              </td>
              <td class="!border-none !bg-white !bg-clip-content !py-1  text-nowrap  !rounded-tr-2xl !rounded-br-2xl">
                <div class="!px-4 !py-6">
                  $50.00
                </div>
              </td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
</div>
