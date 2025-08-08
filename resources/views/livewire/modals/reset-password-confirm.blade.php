<div class="relative">
    <h2 class="text-2xl font-bold !mb-6 text-center select-none">Check Your Email</h2>
    <p class="!mb-4">We've sent a 6-digit verification code to <span class="!text-[#FC7361]">{{ $this->email }}</span>.
        Please enter the code below and create your new password. The code is valid for 1 hour.</p>
    <form wire:submit="submit" class="!space-y-4">
        @csrf
        <div>
            <input wire:model="form.code" type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
              focus:ring-[#FC7361] focus:!border-[#FC7361] outline-none transition-all 
              @error('form.code') !border-red-500 @enderror
              "
                name="code" placeholder="Enter Verification code" autocomplete="one-time-code" id="code-field" />
            @error('form.code')
                <x-form.error>{{ $message }}</x-form.error>
            @enderror
        </div>

        <div>
            <input wire:model="form.password" type="password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
              focus:ring-[#FC7361] focus:!border-[#FC7361] outline-none transition-all 
              @error('form.password') !border-red-500 @enderror
              "
                placeholder="New Passowrd" name="password" autocomplete="one-time-code" id="np-field" />

            @error('form.password')
                <x-form.error>{{ $message }}</x-form.error>
            @enderror
        </div>

        <div>
            <input wire:model="form.password_confirmation" type="password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
              focus:ring-[#FC7361] focus:!border-[#FC7361] outline-none transition-all 
              @error('form.password_confirmation') !border-red-500 @enderror
              "
                placeholder="Confirm New Password" name="password_confirmation" autocomplete="one-time-code"
                id="rnp-field" />


            @error('form.password_confirmation')
                <x-form.error>{{ $message }}</x-form.error>
            @enderror
        </div>

        @if ($this->resend)
            <div x-data="timer({{ $this->resend }})" x-init="start()" class="" wire:on.clear-timer="clearTimer">
                <span>The code has already been sent. Resending will be available after:</span>
                <span x-text="formattedTime"></span>
            </div>

            <a wire:click.prevent="$dispatch('openModal', {modalName: 'backup'})" href="#"
                class="w-full inline-block !text-[#FC7361] hover:!text-[#484134] hover:cursor-pointer !mb-2 font-medium !py-2.5 !rounded-lg transition">
                If you possess a backup code, please enter it here to restore access.
            </a>
        @else
            <a wire:click.prevent="resendCode"
                class="w-full inline-block !text-[#FC7361] hover:!text-[#484134] hover:cursor-pointer !mb-2 font-medium !py-2.5 !rounded-lg transition">
                Didn't receive the code? Resend Code
            </a>
        @endif

        <div class="flex justify-start items-stretch gap-3">
            <a wire:click.prevent="$dispatch('openModal', { modalName: 'reset-password' })" href="#"
                class="w-full border text-center !text-[#FC7361] !border-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] font-medium !py-2.5 !rounded-lg transition">
                Back
            </a>

            <button
                class="w-full !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
                Reset Password
            </button>
        </div>
    </form>
</div>

@push('js')
    <script>
        function timer(endTime) {
            return {
                endTime: endTime,
                remaining: 0,
                formattedTime: '',
                inter: null,

                start() {
                    this.update();
                    this.inter = setInterval(() => {
                        this.update();
                    }, 1000);
                },

                update() {
                    let now = Date.now();
                    this.remaining = Math.max(0, Math.floor((this.endTime - now) / 1000));

                    let hours = Math.floor(this.remaining / 3600);
                    let minutes = Math.floor((this.remaining % 3600) / 60);
                    let seconds = this.remaining % 60;

                    this.formattedTime =
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');

                    console.log(minutes, seconds);
                    console.log(minutes == 0 && seconds == 0);
                    if (minutes == 0 && seconds == 0) {
                        clearInterval(this.inter);
                        Livewire.dispatch('clearTimer');
                    }
                }
            }
        }
    </script>
@endpush
