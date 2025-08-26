<div>
  <div class="flex justify-between items-center mb-4">
    <div class="font-bold text-2xl">Reviews</div>
    <div class="block">
      <label class="text-gray" for="sorting-reviews">Sort By:</label>
      <select
        wire:model.live="sorting" 
        id="sorting-reviews"
        class="outline-0 pr-1 hover:cursor-pointer"
        >
        <option value="">Newest First 1</option>
        <option value="">Newest First 2</option>
        <option value="">Newest First 3</option>
      </select>
    </div>
  </div>
  <div class="overflow-x-scroll scrollbar-custom mb-4">
    <table class="table">
      <thead>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </thead>
      <tbody>
        @for($i = 0; $i < 3; $i++)
          <tr>
            <td class="!border-b-gray/15 !py-4 text-nowrap">
              <div class="flex justify-start items-start gap-2">
                <div class="!w-12 !h-12 rounded-full overflow-hidden shrink-0">
                  <img class="!w-full !h-full object-cover" src="{{ auth()->user()->avatar }}" alt="Avatar">
                </div>
                <div class="flex flex-col">
                  <p>@talmaev1</p>
                  <p class="font-thin text-gray text-sm">
                    <span>01.22.2025</span>
                    <span>10:00</span>
                  </p>
                  <p class="min-w-xs max-w-lg whitespace-normal">Deceives, 4 hours and did not issue the order. Does not cancel! I do not recommend!</p>
                </div>
              </div>
            </td>
            <td class="!border-b-gray/15 !py-4 ">
              <div class="flex items-center justify-start gap-2">
                <div class="!w-14 !h-22 rounded overflow-hidden shrink-0">
                  <img class="w-full h-full object-cover" src="http://localhost:9990/storage/images/product_2.jpg" alt="Image">
                </div>
                <div class="min-w-xs">
                  <p>A Guide to Getting to Know North Korea</p>
                </div>
              </div>
            </td>
            <td class="!border-b-gray/15 !py-4 text-nowrap align-middle ">
              <div class="flex justify-center items-center h-full">
                <span class="text-yellow">@include('icons.star')</span>
                <span>4,9</span>
              </div>
            </td>
            <td class="!border-b-gray/15 !py-4 align-middle">
              <div class="flex justify-center items-center h-full">
                <x-link>Reply</x-link>
              </div>
            </td>
          </tr>
        @endfor
      </tbody>
      <tfoot></tfoot>
    </table>
  </div>
  <div class="text-center">
    <x-link>Show More</x-link>
  </div>
</div>
