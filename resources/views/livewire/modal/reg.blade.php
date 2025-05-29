<div class="max-w-md w-full bg-white rounded-xl shadow-lg !p-4 md:!p-8 relative">
  <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center select-none">Sign Up</h2>
  
  <form wire:submit="reg" class="!space-y-4">
    @csrf
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
      <input 
        wire:model="email"
        type="email" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all @if(array_key_exists('reg.email', $this->errors)) !border-red-500 @endif"
        placeholder="your@email.com"
        name="email"
        autocomplete="off"
      />
      @include('livewire.modal.error_message', ['key' => 'reg.email'])
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
      <input 
        wire:model="password"
        type="password" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all @if(array_key_exists('reg.password', $this->errors)) !border-red-500 @endif"
        placeholder="Password"
        name="password"
      />
      @include('livewire.modal.error_message', ['key' => 'reg.password'])
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Repeat password</label>
      <input 
        wire:model="repeat_password"
        type="password" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all @if(array_key_exists('reg.repeat_password', $this->errors)) !border-red-500 @endif"
        placeholder="Repeat password"
        name="repeat_password"
      />

      @include('livewire.modal.error_message', ['key' => 'reg.repeat_password'])
    </div>


    <div class="flex items-center justify-between">
      <label class="flex justify-start items-center">
        <input wire:model="as_seller" type="checkbox" name="as_seller" class="!rounded !border-gray-300 !text-orange-600 !focus:ring-orange-500 checked:bg-orange-500"/>
        <span class="ml-2 text-sm text-gray-600">Sign up as a seller</span>
      </label>
    </div>

    <button class="w-full !bg-orange-400 hover:!bg-orange-600 text-white font-medium !py-2.5 !rounded-lg transition-colors transition">
      Sign Up
    </button>
  </form>


  <div class="!mt-6 text-center text-sm text-gray-600">
    Already have an account?
    <a wire:click="openAuth" href="#" class="!text-orange-400 hover:!text-orange-600 font-medium transition">Sign in</a>
  </div>
</div>