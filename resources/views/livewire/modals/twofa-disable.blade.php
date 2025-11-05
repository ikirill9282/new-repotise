<div>
  <div class="pb-6 mb-4 border-b-1 border-gray/30">
    {{-- HEADER --}}
    <div class="text-2xl font-semibold mb-3">Disable Two-Factor Authentication?</div>
    
    {{-- DESCRIPTION --}}
    <div class="">
      Disabling 2FA will reduce your account security. To confirm, please enter a verification code from your authenticator app.
    </div>
  </div>

  <div class="mb-4">
    <x-form.input 
      name="code"
      label="Verification Code" 
      placeholder="Enter 6-digit code" 
      data-input="integer"
      :tooltip="false"
      wire:model.defer="code"
    />

    <div class="flex justify-center items-center py-4">
      <div class="border-b-1 !border-gray/40 grow"></div>
      <div class="px-2">OR</div>
      <div class="border-b-1 !border-gray/40 grow"></div>
    </div>

    <x-form.input 
      name="backupCode"
      label="Backup Reset Code" 
      placeholder="Code" 
      :tooltip="false"
      wire:model.defer="backupCode"
    />
  </div>

  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="!text-sm sm:!text-base !w-auto !text-gray !bg-light !border-light" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn 
      class="!text-sm sm:!text-base !w-auto sm:!px-12 text-nowrap" 
      outlined
      wire:click.prevent="openBackup"
    >
      Having Trouble?
    </x-btn>
    <x-btn 
      class="!text-sm sm:!text-base !grow" 
      wire:click.prevent="disable"
      wire:loading.attr="disabled"
      wire:target="disable"
    >
      <span wire:loading.remove wire:target="disable">Disable 2FA</span>
      <span wire:loading wire:target="disable">Processing...</span>
    </x-btn>
  </div>

</div>
