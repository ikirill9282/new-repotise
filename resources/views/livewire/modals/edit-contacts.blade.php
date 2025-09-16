<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Edit Contact Information</div>
  
  <div class="flex flex-col !gap-2 items-stretch justify-start mb-4">
    <x-form.input 
      placeholder="Add Contact Information"
      :tooltipModal="true"
      tooltipText="Enter your public contact for business inquiries & collaborations (e.g., a dedicated email)." 
    />
    <x-form.input 
      placeholder="Add Contact Information"
      :tooltipModal="true"
      tooltipText="Enter your public contact for business inquiries & collaborations (e.g., a dedicated email)." 
    />
  </div>

  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="!py-2 !w-auto !px-6" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!py-2 !grow" >Save</x-btn>
  </div>
  
</div>
