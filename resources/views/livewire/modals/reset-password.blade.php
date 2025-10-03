<div class="relative">

  <h2 class="text-2xl font-bold text-center select-none !mb-6">Forgrot password?</h2>

  <div class="!mb-6 text-center">
    Please enter the email address associated with your account. We'll send you a verification code to reset your password.
  </div>

  <form wire:submit="submit" class="!space-y-4">
    @csrf
    <x-form.input wire:model="form.email" name="email" placeholder="Email Address" :tooltipModal="true" />

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