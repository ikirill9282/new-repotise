<div>
    {{-- HEADER --}}
    <div class="text-2xl font-semibold mb-4">Change Email Address</div>
    
    {{-- DESCRIPTION --}}
    <div class="pb-6 mb-4 border-b-1 border-gray/30">
      For your security, please confirm your current password and enter the new email address you wish to use. We'll send a verification link to the new address to complete the change.
    </div>

    <form action="" class="!mb-6">
      <x-form.input label="Current Password" placeholder="Enter your current password" class="mb-3" />
      <x-form.input label="New Email Address" placeholder="Enter your new email address" />
    </form>

    {{-- BUTTONS --}}
    <div class="flex justify-center items-center gap-3">
      <x-btn class="!text-sm sm:!text-base !w-auto sm:!px-12" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
      <x-btn class="!text-sm sm:!text-base !grow" >Send Verification Link</x-btn>
    </div>
</div>
