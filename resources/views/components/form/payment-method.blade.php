@props([
  'name' => null,
  'id' => uniqid(),
  'tooltip' => true,
  'tooltipText' => null,
  'editor' => true,
  'icons' => false,
  'brand' => 'Visa',
  'last4' => 1234,
  'value' => null,
])

<div class="w-full bg-light rounded !py-2 !px-4 text-sm sm:text-base @if($tooltip) !pr-6 @endif relative flex items-center justify-between group gap-2 {{ $attributes->get('class') }}">
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

  <div x-data="{ open: false }" class="relative flex-shrink-0">
    <button type="button"
      class="p-2 rounded-full hover:bg-white/60 transition flex items-center justify-center focus:outline-none"
      x-on:click.stop="open = !open"
      aria-haspopup="true"
      :aria-expanded="open.toString()"
    >
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray">
        <circle cx="12" cy="5" r="1.5"/>
        <circle cx="12" cy="12" r="1.5"/>
        <circle cx="12" cy="19" r="1.5"/>
      </svg>
    </button>

    <div x-cloak x-show="open" x-transition
      x-on:click.away="open = false"
      class="absolute right-0 mt-2 w-28 bg-white rounded-lg border border-gray/20 shadow-lg py-1 z-10">
      <button type="button"
        class="w-full px-3 py-2 text-left text-sm text-red-500 hover:bg-light transition"
        wire:click.prevent="deletePaymentMethod('{{ $value }}')"
        x-on:click="open = false"
      >
        Delete
      </button>
    </div>
  </div>

  @if($tooltip && filled($tooltipText))
    <x-tooltip :message="$tooltipText" class="right-3"></x-tooltip>
  @endif

  @if ($icons)
    <div class="flex">
      <img src="{{ asset('assets/img/icons/visa.svg') }}" alt="Visa">
      <img src="{{ asset('assets/img/icons/american-express.svg') }}" alt="AmericanExpress">
      <img src="{{ asset('assets/img/icons/master-card.svg') }}" alt="Mastercard">
    </div>
  @endif
</div>