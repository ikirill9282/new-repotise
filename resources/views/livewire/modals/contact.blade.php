<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Contact {{ $this->getRecipient()?->getName() }}</div>
  <div class="!mb-4">
    <x-form.text-counter wire:model="text" name="text" :button="false" max="500" placeholder="Add a message..."></x-form.text-counter>
  </div>

  <div class="text-center">
    <x-btn wire:click.prevent="submit" class="!max-w-[9rem] !inline-block !py-2">Done</x-btn>
  </div>
</div>
