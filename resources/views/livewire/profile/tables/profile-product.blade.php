<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}

    <div class="">
      <x-form.checkbox 
        label="Choose a product to showcase on your Creator's Page"
        wire:model="all_checked"
      />
    </div>
    <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
      <table class="table">
        <tbody>
          @for($i = 0; $i < 10; $i++)
            <tr>
              <td class="!border-b-gray/15 !py-4 text-nowrap">10.10.2025 10:00</td>
              <td class="!border-b-gray/15 !py-4 text-nowrap">Donor Name</td>
              <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">$300</td>
              <td class="!border-b-gray/15 !py-4">
                <div class="flex items-center gap-2 hover:!text-active {{ $i % 2 == 0 ? '!text-gray' : 'text-active' }}">
                  <div class="">@include('icons.message')</div>
                  <x-link>Show message</x-link>
                </div>
              </td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
</div>
