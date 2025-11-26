<div class="relative">
    <h2 class="text-2xl font-bold !mb-6 text-center select-none">Check Your Email</h2>
    <p class="!mb-4">We've sent a 6-digit verification code to <span class="!text-[#FC7361]">{{ $this->email }}</span>.
        Please enter the code below and create your new password. The code is valid for 1 hour.</p>
    <form wire:submit="submit" class="!space-y-4">
        @csrf
        <x-form.input wire:model="form.code" name="code" placeholder="Enter Verification code" autocomplete="one-time-code" id="code-field" />

        <div x-data="passwordStrength('form.password')" class="">
          <x-form.input wire:model="form.password" name="password" x-bind:type="type" placeholder="Password" x-on:input="checkStrength($event.target.value)" :tooltipModal="true" tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols.">
            <x-slot name="icon">
              <div x-on:click="() => type = (type == 'password') ? 'text' : 'password' " class="absolute top-1/2 right-9 translate-y-[-50%] hover:cursor-pointer">
                <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
              </div>
            </x-slot>
          </x-form.input>
          <div x-show="password.length > 0" x-transition class="mt-2">
            <div class="flex items-center gap-2 mb-1">
              <div class="flex-1 h-1.5 bg-gray/20 rounded-full overflow-hidden">
                <div x-bind:class="{
                  'bg-red-500': strength === 'weak',
                  'bg-yellow-500': strength === 'medium',
                  'bg-green-500': strength === 'strong'
                }" x-bind:style="'width: ' + (strength === 'weak' ? '33%' : strength === 'medium' ? '66%' : '100%')" class="h-full transition-all duration-300"></div>
              </div>
              <span x-bind:class="{
                'text-red-500': strength === 'weak',
                'text-yellow-500': strength === 'medium',
                'text-green-500': strength === 'strong'
              }" x-text="strengthText" class="text-xs font-medium"></span>
            </div>
            <p x-show="strength === 'weak'" class="text-xs text-red-500 mt-1">This password is too weak. Please use a medium or strong password with uppercase and lowercase letters, numbers, and symbols.</p>
          </div>
        </div>

        <div x-data="{ type: 'password' }" class="">
          <x-form.input wire:model="form.password_confirmation" name="password_confirmation" x-bind:type="type" placeholder="Create a password" :tooltipModal="true" tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols.">
            <x-slot name="icon">
              <div x-on:click="() => type = (type == 'password') ? 'text' : 'password' " class="absolute top-1/2 right-9 translate-y-[-50%] hover:cursor-pointer">
                <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
              </div>
            </x-slot>
          </x-form.input>
        </div>

        @if ($this->resend)
            <div x-data="timer({{ $this->resend }})" x-init="start()" class="" wire:on.clear-timer="clearTimer">
                <span>The code has already been sent. Resending will be available after:</span>
                <span x-text="formattedTime"></span>
            </div>
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

                    if (minutes == 0 && seconds == 0) {
                        clearInterval(this.inter);
                        Livewire.dispatch('clearTimer');
                    }
                }
            }
        }

        function passwordStrength(wireModel) {
            return {
                type: 'password',
                password: '',
                strength: 'weak',
                strengthText: 'Weak',
                
                init() {
                    this.$nextTick(() => {
                        const input = this.$el.querySelector('input[name="password"]');
                        if (input && input.value) {
                            this.checkStrength(input.value);
                        }
                    });
                },
                
                checkStrength(value) {
                    this.password = value || '';
                    if (!this.password) {
                        this.strength = 'weak';
                        this.strengthText = 'Weak';
                        return;
                    }
                    
                    let score = 0;
                    const length = this.password.length;
                    
                    // Length checks
                    if (length >= 8) score++;
                    if (length >= 12) score++;
                    if (length >= 16) score++;
                    
                    // Character variety
                    if (/[a-z]/.test(this.password)) score++; // lowercase
                    if (/[A-Z]/.test(this.password)) score++; // uppercase
                    if (/\d/.test(this.password)) score++; // numbers
                    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(this.password)) score++; // special chars
                    
                    // Additional checks
                    if (length >= 8 && /[a-z]/.test(this.password) && /[A-Z]/.test(this.password) && /\d/.test(this.password)) {
                        score++; // has lowercase, uppercase, and number
                    }
                    if (length >= 8 && /[a-z]/.test(this.password) && /[A-Z]/.test(this.password) && /\d/.test(this.password) && /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(this.password)) {
                        score++; // has all character types
                    }
                    
                    // Determine strength
                    if (score <= 3) {
                        this.strength = 'weak';
                        this.strengthText = 'Weak';
                    } else if (score <= 6) {
                        this.strength = 'medium';
                        this.strengthText = 'Medium';
                    } else {
                        this.strength = 'strong';
                        this.strengthText = 'Strong';
                    }
                }
            }
        }
    </script>
@endpush
