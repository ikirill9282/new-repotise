<div>

  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Description of file [номер файла, 1-8]</div>
  
  {{-- CONTENT --}}
  <div class="!mb-6">
    <div class="" x-data="{
          len: 0,
          max: 200,
          setLen(val) {
            this.len = val;
          }
        }"
      >
      <x-form.textarea 
        placeholder="Text your message"
        :tooltip="true"
        class="min-h-24"
        x-on:input="(evt) => {
          const len = evt.target.value.length;
          if (len <= max) setLen(len);
        }"
        x-ref="ta"
      ></x-form.textarea>
      <div class="text-sm !text-gray text-right mt-2">
        <span x-html="len"></span>
        <span>/</span>
        <span x-html="max"></span>
      </div>
    </div>
  </div>

  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="!py-2 !w-auto !px-6" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!py-2 !grow" >Save</x-btn>
  </div>
</div>
