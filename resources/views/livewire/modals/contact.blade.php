<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Contact {{ $user->getName() }}</div>
  <div class="!mb-6">
    
    @if(empty($contacts))
      <div class="text-center text-lg">The {{ $user->getName() }} has not provided contact details yet.</div>
    @else
      <div class="flex flex-col justify-start items-stretch !gap-3">
        @foreach ($contacts as $contact)
          <div class="flex items-center justify-between !p-3 rounded bg-light copyToClipboard hover:cursor-pointer" data-target="contact1">
            <div class="" data-copyId="contact1">{{ $contact }}</div>
            <div class="">@include('icons.copy')</div>
          </div>
        @endforeach
      </div>
    @endif

  </div>

  <div class="text-center">
    <x-btn wire:click.prevent="$dispatch('closeModal')" class="!max-w-[9rem] !inline-block !py-2">Done</x-btn>
  </div>
</div>
