<div class="w-full">
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Your Product</div>

  {{-- DESCRIPTION --}}
  <div class="mb-4">
    <div class="text-lg font-semibold mb-3">Description from Creator:</div>
    <div class="">This luxury watch combines Swiss quality, stylish design and excellent performance. Classics. Original wristwatch. The mechanism has 8 stones. The round steel case is decorated with diamond pavé. Mother-of-pearl dial. No second hand. Markers in the form of Roman numerals. The date window is located</div>
  </div>

  {{-- FILES --}}
  <div class="overflow-y-scroll scrollbar-custom max-h-50 flex flex-col gap-3 mb-6 product-popup-images">
    <div class="flex justify-start items-center gap-4 product-popup-images__item">
      <x-link class="flex justify-start items-center gap-3 border-none group bg-light p-3 rounded-lg product-popup-images__button">
        <span class="text-active">@include('icons.docs')</span>
        <span class="border-b border-dashed transition group-hover:border-active">Download</span>
      </x-link>
      <div class="">This luxury watch combines Swiss quality, stylish</div>
    </div>
    <div class="flex justify-start items-center gap-4 product-popup-images__item">
      <x-link class="flex justify-start items-center gap-3 border-none group bg-light p-3 rounded-lg product-popup-images__button">
        <span class="text-active">@include('icons.docs')</span>
        <span class="border-b border-dashed transition group-hover:border-active">Download</span>
      </x-link>
      <div class="">This luxury watch combines Swiss quality, stylish</div>
    </div>
    <div class="flex justify-start items-center gap-4 product-popup-images__item">
      <x-link class="flex justify-start items-center gap-3 border-none group bg-light p-3 rounded-lg product-popup-images__button">
        <span class="text-active">@include('icons.docs')</span>
        <span class="border-b border-dashed transition group-hover:border-active">Download</span>
      </x-link>
      <div class="">This luxury watch combines Swiss quality, stylish</div>
    </div>
    <div class="flex justify-start items-center gap-4 product-popup-images__item">
      <x-link class="flex justify-start items-center gap-3 border-none group bg-light p-3 rounded-lg product-popup-images__button">
        <span class="text-active">@include('icons.docs')</span>
        <span class="border-b border-dashed transition group-hover:border-active">Download</span>
      </x-link>
      <div class="">This luxury watch combines Swiss quality, stylish</div>
    </div>
    <div class="flex justify-start items-center gap-4 product-popup-images__item">
      <x-link class="flex justify-start items-center gap-3 border-none group bg-light p-3 rounded-lg product-popup-images__button">
        <span class="text-active">@include('icons.docs')</span>
        <span class="border-b border-dashed transition group-hover:border-active">Download</span>
      </x-link>
      <div class="">This luxury watch combines Swiss quality, stylish</div>
    </div>
    <div class="flex justify-start items-center gap-4 product-popup-images__item">
      <x-link class="flex justify-start items-center gap-3 border-none group bg-light p-3 rounded-lg product-popup-images__button">
        <span class="text-active">@include('icons.docs')</span>
        <span class="border-b border-dashed transition group-hover:border-active">Download</span>
      </x-link>
      <div class="">This luxury watch combines Swiss quality, stylish</div>
    </div>
  </div>

  {{-- IMAGES --}}
  <div class="flex flex-col gap-3 pb-6 mb-4 border-b-1 border-gray/30">
    <div 
      class="copyToClipboard flex justify-start !gap-3 group transition hover:text-active hover:cursor-pointer"
      data-target="file1"
      >
      <div class="bg-light rounded-lg w-full !p-3 flex justify-between items-center">
        <span data-copyId="file1">https://ru.freepik.com </span>
        <span>@include('icons.copy')</span>
      </div>
      <x-btn outlined class="w-auto !rounded">View</x-btn>
    </div>
    <div 
      class="copyToClipboard flex justify-start !gap-3 group transition hover:text-active hover:cursor-pointer"
      data-target="file2"
      >
      <div class="bg-light rounded-lg w-full !p-3 flex justify-between items-center">
        <span data-copyId="file2">https://ru.freepik.com </span>
        <span>@include('icons.copy')</span>
      </div>
      <x-btn outlined class="w-auto !rounded">View</x-btn>
    </div>
    <div 
      class="copyToClipboard flex justify-start !gap-3 group transition hover:text-active hover:cursor-pointer"
      data-target="file3"
      >
      <div class="bg-light rounded-lg w-full !p-3 flex justify-between items-center">
        <span data-copyId="file3">https://ru.freepik.com </span>
        <span>@include('icons.copy')</span>
      </div>
      <x-btn outlined class="w-auto !rounded">View</x-btn>
    </div>
  </div>

  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3 max-w-xl mx-auto">
    <x-btn class="!text-sm sm:!text-base w-auto m-0" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!text-sm sm:!text-base w-auto m-0 grow" >Download All Files (ZIP)</x-btn>
  </div>
</div>
