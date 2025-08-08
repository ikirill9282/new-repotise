<div class="relative">

  <h2 class="text-2xl font-bold text-center select-none !mb-6">Use Backup Code</h2>
  <p class="!mb-4">Please enter your Backup Reset Code to regain access to your account.</p>
  <form wire:submit="submit" class="!space-y-4">
    @csrf
    <div>
      <input 
        wire:model="form.code"
        type="text" 
        class="w-full px-4 !py-3 border border-gray-300 rounded-lg focus:ring-2 
              focus:ring-[#FC7361] focus:border-[#FC7361] outline-none transition-all 
              {{-- @if(array_key_exists('backup', $this->errors)) !border-red-500 @endif --}}
              "
        placeholder="Backup Code"
        name="code"
        autocomplete="one-time-code"
      />
      {{-- @include('livewire.modal.error_message', ['key' => 'backup']) --}}
    </div>

    <div class="flex justify-start items-stretch gap-3">
      <a wire:click.prevent="$dispatch('openModal', {modalName: 'auth'})" href="#" class="px-4 border text-center !text-[#FC7361] !border-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] font-medium !py-2.5 !rounded-lg transition">
        Back
      </a>

      <button class="w-full !grow !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
        Reset two-factor authentication
      </button>
    </div>
  </form>

</div>