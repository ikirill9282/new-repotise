<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Connect Your Social Media</div>
  <div class="flex flex-col items-stretch justify-start !gap-4 pb-6 mb-4 border-b-1 border-gray/30">

    @foreach ($this->socials as $key => $social)
      <div class="flex justify-start items-center !gap-2">
        <div class="">
          <img class="!w-10" src="{{ $this->getIcon($key) }}" alt="YouTube">
        </div>
        <div class="grow">
          <x-form.input 
            :tooltip="false"
            wire:model="socials.{{ $key }}"
            inputWrapClass="!py-2"
            placeholder="Add Link..."
          />
        </div>
      </div>
      @error($key)
        <div class="!mt-2 text-red-500">{{ $message }}</div>
      @enderror
    @endforeach
  </div>


  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="!py-2 !w-auto !px-6" gray wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn wire:click.prevent="submit" class="!py-2 !grow" >Save</x-btn>
  </div>


</div>
