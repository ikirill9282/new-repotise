<script>
window.passwordStrength = window.passwordStrength || function passwordStrength(wireModel) {
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
            if (length >= 8) score++;
            if (length >= 12) score++;
            if (length >= 16) score++;
            if (/[a-z]/.test(this.password)) score++;
            if (/[A-Z]/.test(this.password)) score++;
            if (/\d/.test(this.password)) score++;
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(this.password)) score++;
            if (length >= 8 && /[a-z]/.test(this.password) && /[A-Z]/.test(this.password) && /\d/.test(this.password)) {
                score++;
            }
            if (length >= 8 && /[a-z]/.test(this.password) && /[A-Z]/.test(this.password) && /\d/.test(this.password) && /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(this.password)) {
                score++;
            }
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
};
</script>

<div class="relative" wire:key="set-password-wrapper-{{ $this->getId() }}">
    <h2 class="text-2xl font-bold !mb-6 text-center select-none">Set Your Password</h2>
    
    <form wire:submit="submit" wire:key="set-password-form-{{ $this->getId() }}" class="!space-y-4">
        @csrf
        
        <div x-data="passwordStrength('form.password')" class="">
            <x-form.input 
                name="password" 
                x-bind:type="type" 
                placeholder="Password" 
                autocomplete="new-password" 
                wire:model="form.password"
                x-on:input="checkStrength($event.target.value)"
                :tooltipModal="true" 
                tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols."
            >
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
                <p x-show="strength === 'weak'" class="text-xs text-red-500 mt-1">The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.</p>
            </div>
        </div>
        @error('form.password')
            <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
        @enderror

        <div x-data="{ type: 'password' }" class="">
            <x-form.input 
                name="password_confirmation" 
                x-bind:type="type" 
                placeholder="Confirm Password" 
                autocomplete="new-password" 
                :tooltipModal="true" 
                tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols."
                wire:model="form.password_confirmation"
            >
                <x-slot name="icon">
                    <div x-on:click="() => type = (type == 'password') ? 'text' : 'password' " class="absolute top-1/2 right-9 translate-y-[-50%] hover:cursor-pointer">
                        <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
                    </div>
                </x-slot>
            </x-form.input>
        </div>
        @error('form.password_confirmation')
            <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
        @enderror

        <div class="flex justify-start items-stretch gap-3">
            <button type="submit"
                class="w-full !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
                Set Password
            </button>
        </div>
    </form>
</div>
