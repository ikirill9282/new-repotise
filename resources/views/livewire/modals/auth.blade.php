<div class="relative">
  <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center select-none">Sign In</h2>
  
  <form wire:submit="submit" class="!space-y-4">
    @csrf
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
      <input 
        wire:model="form.email"
        type="email" 
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FC7361] focus:!border-[#FC7361] outline-none transition-all 
              @error('form.email') !border-red-500 @enderror
              "
        placeholder="your@email.com"
        name="email"
      />
      @error('form.email')
        <x-form.error>{{ $message }}</x-form.error>
      @enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
      <input 
        wire:model="form.password"
        type="password" 
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FC7361] focus:!border-[#FC7361] outline-none transition-all
              @error('form.password') !border-red-500 @enderror
              "
        placeholder="••••••••"
        name="password"
      />
      @error('form.password')
        <x-form.error>{{ $message }}</x-form.error>
      @enderror
    </div>

    <div class="flex items-center justify-between">
      <label class="!flex justify-start items-center  gap-2">
        <input wire:model="form.remember" type="checkbox" name="remember" class="!rounded !border-gray-300 !text-[#FC7361] hover:!text-[#484134] !focus:ring-[#FC7361] checked:bg-[#FC7361]"/>
        <span class="ml-2 text-sm text-gray-600 hover:cursor-pointer hover:text-[#FC7361] transition">Remember me</span>
      </label>
      <a wire:click.prevent="$dispatch('openModal', { modalName: 'reset-password' })" href="#" class="disabled text-sm !text-[#FC7361] hover:!text-[#484134] transition">Forgot password?</a>
    </div>

    <button class="w-full !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
      Sign In
    </button>
  </form>

  <div class="!mt-6 text-center text-sm text-gray-600">
    Don't have an account? 
    <a wire:click.prevent="$dispatch('openModal', {modalName: 'register'})" href="#" class="!text-[#FC7361] hover:!text-[#484134] font-medium transition">Sign up</a>
  </div>
</div>