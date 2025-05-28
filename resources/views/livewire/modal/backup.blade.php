<div class="max-w-md w-full bg-white rounded-xl shadow-lg !p-4 md:!p-8 relative">

  <h2 class="text-2xl font-bold text-center select-none !mb-6">Use Backup Code</h2>
  <p class="!mb-4">Please enter your Backup Reset Code to regain access to your account.</p>
  <form wire:submit="useBackupCode" class="!space-y-4">
    @csrf
    <div>
      <input 
        wire:model="backup"
        type="text" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all @if(array_key_exists('backup', $this->errors)) !border-red-500 @endif"
        placeholder="Backup Code"
        name="backup"
        autocomplete="one-time-code"
      />
      @include('livewire.modal.error_message', ['key' => 'backup'])
    </div>

    <div class="flex justify-start items-stretch gap-3">
      <a wire:click="openAuth" href="#" class="px-4 border text-center !text-orange-400 !border-orange-400 hover:!border-orange-600 hover:!text-orange-600 font-medium !py-2.5 !rounded-lg transition-colors transition">
        Back
      </a>

      <button class="w-full !grow !bg-orange-400 hover:!bg-orange-600 text-white font-medium !py-2.5 !rounded-lg transition-colors transition">
        Reset two-factor authentication
      </button>
    </div>
  </form>

</div>