<div class="relative">

  <h2 class="text-2xl font-bold text-center select-none !mb-6">Forgrot password?</h2>
  
  <form wire:submit="submit" class="!space-y-4">
    @csrf
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
      <input 
        wire:model="form.email"
        type="email" 
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2
         focus:ring-[#FC7361] focus:!border-[#FC7361] outline-none transition-all 
          @error('form.email') !border-red-500 @enderror
          "
        placeholder="your@email.com"
        name="email"
      />
      @error('form.email')
        <x-form.error>{{ $message }}</x-form.error>
      @enderror
    </div>

    <div class="flex justify-start items-stretch gap-3">
      <a wire:click.prevent="$dispatch('openModal', {modalName: 'auth'})" href="#" class="w-full border text-center !text-[#FC7361] !border-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] font-medium !py-2.5 !rounded-lg transition">
        Back to Login
      </a>

      <button class="w-full !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
        Send reset code
      </button>
    </div>
  </form>

</div>