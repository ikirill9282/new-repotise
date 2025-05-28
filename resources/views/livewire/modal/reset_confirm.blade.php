<div class="max-w-md w-full bg-white rounded-xl shadow-lg !p-4 md:!p-8 relative">
  <h2 class="text-2xl font-bold !mb-6 text-center select-none">Check Your Email</h2>
  <p class="!mb-4">We've sent a 6-digit verification code to {{ $this->email }}. Please enter the code below and create your new password. The code is valid for 24 hours.</p>
  <form wire:submit="confirmNewPassword" class="!space-y-4">
    @csrf
    <div>
      <input 
        wire:model="code"
        type="text" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all @if(array_key_exists('code', $this->errors)) !border-red-500 @endif"
        name="code"
        placeholder="Enter Verification code"
        autocomplete="one-time-code"
        id="code-field"
      />
      @include('livewire.modal.error_message', ['key' => 'code'])
    </div>

    <div>
      <input 
        wire:model="new_password"
        type="password" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all @if(array_key_exists('new_password', $this->errors)) !border-red-500 @endif"
        placeholder="New Passowrd"
        name="new_password"
        autocomplete="one-time-code"
        id="np-field"
      />
      @include('livewire.modal.error_message', ['key' => 'new_password'])
    </div>

    <div>
      <input 
        wire:model="repeat_new_password"
        type="password" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all @if(array_key_exists('repeat_new_password', $this->errors)) !border-red-500 @endif"
        placeholder="Confirm New Password"
        name="repeat_password"
        autocomplete="one-time-code"
        id="rnp-field"
      />

      @include('livewire.modal.error_message', ['key' => 'repeat_new_password'])
    </div>

    <a class="w-full inline-block !text-orange-400 hover:!text-orange-600 hover:cursor-pointer !mb-2 font-medium !py-2.5 !rounded-lg transition-colors transition">
      Didn't receive the code? Resend Code
    </a>

    <div class="flex justify-start items-stretch gap-3">
      <a wire:click="openAuth" href="#" class="w-full border text-center !text-orange-400 !border-orange-400 hover:!border-orange-600 hover:!text-orange-600 font-medium !py-2.5 !rounded-lg transition-colors transition">
        Back 
      </a>

      <button class="w-full !bg-orange-400 hover:!bg-orange-600 text-white font-medium !py-2.5 !rounded-lg transition-colors transition">
        Reset Password
      </button>
    </div>
  </form>
</div>