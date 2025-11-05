<div>
    {{-- HEADER --}}
    <div class="text-2xl font-semibold mb-4">Change Email Address</div>
    
    {{-- DESCRIPTION --}}
    <div class="pb-6 mb-4 border-b-1 border-gray/30">
      For your security, please confirm your current password and enter the new email address you wish to use. We'll send a verification link to the new address to complete the change.
    </div>

    <form class="!mb-6">
      <x-form.input 
        name="current_password"
        label="Current Password" 
        type="password"
        placeholder="Enter your current password" 
        class="mb-3"
        :tooltip="false"
        wire:model.defer="current_password"
        autocomplete="current-password"
      />
      <x-form.input 
        name="email"
        label="New Email Address" 
        type="email"
        placeholder="Enter your new email address" 
        :tooltip="false"
        wire:model.defer="email"
        autocomplete="email"
      />
    </form>

    {{-- BUTTONS --}}
    <div class="flex justify-center items-center gap-3">
      <x-btn class="!text-sm sm:!text-base !w-auto sm:!px-12" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
      <x-btn 
        class="!text-sm sm:!text-base !grow" 
        wire:click.prevent="submit"
        wire:loading.class="pointer-events-none opacity-70"
        wire:target="submit"
      >
        <span wire:loading.remove wire:target="submit">Send Verification Link</span>
        <span wire:loading wire:target="submit">Sending...</span>
      </x-btn>
    </div>
</div>
