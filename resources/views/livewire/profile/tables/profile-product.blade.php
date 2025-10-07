<div>
    @if($products->isEmpty())
      <div class="text-center">
        <div class="sm:text-lg !mb-4">No products added yet.<br>Start showcasing your travel expertise! Add your first product to begin selling.</div>
        <x-btn href="{{ route('profile.products.create') }}" class="sm:!w-auto sm:!px-12">Add New Product</x-btn>
      </div>
    @else

      <div class="mb-4">
        <x-form.checkbox 
          label="Choose a product to showcase on your Creator's Page"
          wire:model="all_checked"
        />
      </div>
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom">
        <div class="flex flex-col items-stretch justify-start gap-4 pb-2">
          @foreach($products as $product)
            <div class="grid grid-cols-[2rem_10rem_minmax(16rem,100%)_minmax(0,5rem)] md:grid-cols-[2rem_12rem_minmax(0,100%)_minmax(0,16rem)] items-center !gap-4 !mb-4 last:!mb-0 max-w-full">
              <div class="">
                <x-form.checkbox 
                  label=""
                  :id="\Illuminate\Support\Facades\Crypt::encrypt($product->id)"
                />
              </div>
              <div class="rounded-lg overflow-hidden h-24 md:h-30">
                <img class="w-full h-full object-cover" src="{{ $product->preview->image }}" alt="Preview">
              </div>
              <div class="flex justify-start items-start md:items-center flex-col md:flex-row w-full !gap-16 overflow-hidden">
                <div class="w-full md:w-auto md:overflow-hidden">
                  <div class="text-gray truncate mb-2 w-full">
                    <x-link href="{{ $product->makeUrl() }}" class="!border-0">{{ $product->title }}</x-link>
                  </div>
                  <div class="flex justify-start items-center gap-2 text-sm mb-2">
                      <p class="text-gray bg-light px-2 py-1 rounded-full">{{ \Illuminate\Support\Carbon::parse($product->published_at ?? $product->created_at)->format('d.m.Y') }}</p>
                      <p class="text-gray bg-light px-2 py-1 rounded-full">{{ $product->views }} Views</p>
                  </div>
                  <div class="flex justify-start items-center !gap-2 text-gray">
                    <div class="">@include('icons.like')</div>
                    <div class="">Like</div>
                    <div class="!text-black">{{ $product->favorite_count }}</div>
                  </div>
                </div>
              </div>
              <div class="w-full md:w-auto flex flex-col md:flex-row justify-start md:justify-end items-start md:items-center !gap-3 md:!gap-6 !text-sm md:!text-base">
                <x-link>Duplicate</x-link>
                <x-link href="{{ $product->makeEditUrl() }}">Edit</x-link>
                <x-link>Delete</x-link>
              </div>
            </div>
          @endforeach
        </div>
      </div>

    @endif
</div>
