<div>
    {{-- @if(empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}
    <div class="mb-4">
      <x-form.checkbox 
        label="Choose a product to showcase on your Creator's Page"
        wire:model="all_checked"
      />
    </div>
    <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
      <div class="flex flex-col items-stretch justify-start gap-4 !pb-3">
        @for($i = 0; $i < 10; $i++)
          <div class="grid grid-cols-[2rem_10rem_minmax(16rem,100%)_minmax(0,5rem)] md:grid-cols-[2rem_12rem_minmax(0,100%)_minmax(0,16rem)] items-center !gap-4 !mb-4 last:!mb-0 max-w-full">
            <div class="">
              <x-form.checkbox 
                label=""
                :id="str_shuffle((uniqid() . $i))"
              />
            </div>
            <div class="rounded-lg overflow-hidden h-24 md:h-30">
              <img class="w-full h-full object-cover" src="/storage/images/img_articles.png" alt="">
            </div>
            <div class="flex justify-start items-start md:items-center flex-col md:flex-row w-full !gap-16 overflow-hidden">
              <div class="w-full md:w-auto md:overflow-hidden">
                <div class="text-gray truncate mb-2 w-full">A Guide to Getting to Know North Korea A Guide to A Guide to Getting to Know North Korea</div>
                <div class="flex justify-start items-center gap-2 text-sm mb-2">
                    <p class="text-gray bg-light px-2 py-1 rounded-full">{{ \Illuminate\Support\Carbon::now()->format('d.m.Y') }}</p>
                    <p class="text-gray bg-light px-2 py-1 rounded-full">12345 Views</p>
                </div>
                <div class="">
                  <x-like type="product" count="1234567890" :id="$i"></x-like>
                </div>
              </div>
            </div>
            <div class="w-full md:w-auto flex flex-col md:flex-row justify-start md:justify-end items-start md:items-center !gap-3 md:!gap-6 !text-sm md:!text-base">
              <x-link>Duplicate</x-link>
              <x-link>Edit</x-link>
              <x-link>Delete</x-link>
            </div>
          </div>
        @endfor
      </div>
    </div>
</div>
