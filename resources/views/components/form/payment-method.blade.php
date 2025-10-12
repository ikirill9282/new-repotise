@props([
  'name' => null,
  'id' => uniqid(),
  'tooltip' => true,
  'editor' => true,
  'icons' => false,
  'brand' => 'Visa',
  'last4' => 1234,
  'value' => null,
])

<div class="w-full bg-light rounded !py-2 !px-4 text-sm sm:text-base @if($tooltip) !pr-6 @endif relative flex items-center justify-between group {{ $attributes->get('class') }}">
  <label for="{{ $id }}" class="relative !flex items-center gap-2 text-sm grow hover:cursor-pointer ">
    <div class="w-5 h-5 rounded-full border-1 border-gray transition group-has-checked:bg-active p-1 bg-clip-content"></div>
    <input type="radio" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" class="!w-0 !h-0 !opacity-0" {{ $attributes }} >
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2">
        <div class="">{{ $brand }}</div>
        <div class="text-active">USD</div>
      </div>
      <div class="text-gray">
        <span>****</span>
        <span>****</span>
        <span>****</span>
        <span>{{ $last4 }}</span>
      </div>
    </div>
  </label>

  @if($editor)
    <x-chat.editor 
      baseClass="{{ $tooltip ? '!mr-2' : '' }}"
      wrapClass="!bg-white !p-4 !flex-row !rounded !gap-4" 
      containerClass="!rounded shadow-sm !mr-2"
      :target="$id"
      >
        <x-link>Edit</x-link>
        <x-link>Delete</x-link>
    </x-chat.editor>
  @endif

  @if($tooltip)
    <x-tooltip message="tooltip" class="right-4"></x-tooltip>
  @endif

  @if ($icons)
    <div class="flex">
      <img src="{{ asset('assets/img/icons/visa.svg') }}" alt="Visa">
      <img src="{{ asset('assets/img/icons/american-express.svg') }}" alt="AmericanExpress">
      <img src="{{ asset('assets/img/icons/master-card.svg') }}" alt="Mastercard">
    </div>
  @endif
</div>