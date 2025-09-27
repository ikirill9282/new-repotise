<div>

  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Description of file "{{ $this->filename }}"</div>
  
  {{-- CONTENT --}}
  <div class="!mb-6">
    <x-form.textarea-counter max="200" wire:model="description" placeholder="Text your message"></x-form.textarea-counter>
  </div>

  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="!py-2 !w-auto !px-6" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!py-2 !grow" wire:click.prevent="submit">Save</x-btn>
  </div>
</div>
