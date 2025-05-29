<div class="max-w-md w-full bg-white rounded-xl shadow-lg !p-4 md:!p-8 relative">

  <h2 class="text-2xl font-bold text-center select-none !mb-6">Forgrot password?</h2>
  
  <form wire:submit="sendResetCode" class="!space-y-4">
    @csrf
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
      <input 
        wire:model="email"
        type="email" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all @if(array_key_exists('auth', $this->errors)) !border-red-500 @endif"
        placeholder="your@email.com"
        name="email"
      />
      @include('livewire.modal.error_message', ['key' => 'auth'])
    </div>

    <div class="flex justify-start items-stretch gap-3">
      <a wire:click.prevent="openAuth" href="#" class="w-full border text-center !text-orange-400 !border-orange-400 hover:!border-orange-600 hover:!text-orange-600 font-medium !py-2.5 !rounded-lg transition-colors transition">
        Back to Login
      </a>

      <button class="w-full !bg-orange-400 hover:!bg-orange-600 text-white font-medium !py-2.5 !rounded-lg transition-colors transition">
        Send reset code
      </button>
    </div>
  </form>

</div>