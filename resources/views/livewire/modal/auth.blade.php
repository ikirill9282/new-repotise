<div class="max-w-md w-full bg-white rounded-xl shadow-lg !p-4 md:!p-8 relative">

  <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center select-none">Sign In</h2>
  
  <form wire:submit="auth" class="!space-y-4">
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

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
      <input 
        wire:model="password"
        type="password" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all"
        placeholder="••••••••"
        name="password"
      />
    </div>

    <div class="flex items-center justify-between">
      <label class="flex items-center">
        <input wire:model="remember" type="checkbox" name="remember" class="!rounded !border-gray-300 !text-orange-600 !focus:ring-orange-500 checked:bg-orange-500"/>
        <span class="ml-2 text-sm text-gray-600">Remember me</span>
      </label>
      <a wire:click="resetPassword" href="#" class="disabled text-sm !text-orange-400 hover:!text-orange-600 transition">Forgot password?</a>
    </div>

    <button class="w-full !bg-orange-400 hover:!bg-orange-600 text-white font-medium !py-2.5 !rounded-lg transition-colors transition">
      Sign In
    </button>
  </form>

  <div class="!mt-6 text-center text-sm text-gray-600">
    Don't have an account? 
    <a wire:click="openReg" href="#" class="!text-orange-400 hover:!text-orange-600 font-medium transition">Sign up</a>
  </div>
</div>