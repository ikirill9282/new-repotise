@props([
  'name' => null,
  'id' => uniqid(),
])

<div class="w-full bg-light rounded !py-2 !px-4 !pr-6 relative flex items-center justify-between group">
  <label for="{{ $id }}" class="relative !flex items-center gap-2 text-sm w-full hover:cursor-pointer ">
    <div class="w-5 h-5 rounded-full border-1 border-gray transition group-has-checked:bg-active p-1 bg-clip-content"></div>
    <input type="radio" name="{{ $name }}" id="{{ $id }}" class="!w-0 !h-0 !opacity-0" {{ $attributes }} >
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2">
        <div class="">Visa</div>
        <div class="text-active">USD</div>
      </div>
      <div class="text-gray">
        <span>****</span>
        <span>****</span>
        <span>****</span>
        <span>1234</span>
      </div>
    </div>
  </label>
  <x-chat.editor 
    baseClass="!mr-2"
    wrapClass="!bg-white !p-4 !flex-row !rounded !gap-4" 
    containerClass="!rounded shadow-sm !mr-2"
    :target="$id"
  >
      <x-link>Edit</x-link>
      <x-link>Delete</x-link>
  </x-chat.editor>
  <x-tooltip message="tooltip" class="right-4"></x-tooltip>
</div>