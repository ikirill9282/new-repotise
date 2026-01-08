<script>
// CRITICAL: Define functions immediately before Alpine.js tries to use them
window.timer = window.timer || function timer(endTime) {
    return {
        endTime: endTime,
        remaining: 0,
        formattedTime: '',
        inter: null,
        start() {
            this.update();
            this.inter = setInterval(() => { this.update(); }, 1000);
        },
        update() {
            let now = Date.now();
            this.remaining = Math.max(0, Math.floor((this.endTime - now) / 1000));
            let minutes = Math.floor((this.remaining % 3600) / 60);
            let seconds = this.remaining % 60;
            this.formattedTime = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            if (this.remaining <= 0) {
                clearInterval(this.inter);
                if (window.Livewire && window.Livewire.dispatch) {
                    window.Livewire.dispatch('clearTimer');
                }
            }
        }
    }
};

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

<div class="relative" wire:key="reset-password-confirm-wrapper-{{ $this->getId() }}">
    <h2 class="text-2xl font-bold !mb-6 text-center select-none">Check Your Email</h2>
    <p class="!mb-4">We've sent a 6-digit verification code to <span class="!text-[#FC7361]">{{ $this->email }}</span>.
        Please enter the code below and create your new password. The code is valid for 1 hour.</p>
    <div x-data="{ 
        componentId: @js($this->getId()),
        updateField(field, value) {
            const component = Livewire.find(this.componentId);
            if (component && typeof component.set === 'function') {
                component.set('form.' + field, value);
            }
        },
        submitForm() {
            console.log('submitForm called', { componentId: this.componentId });
            
            // Try to find the component and call method directly
            const component = Livewire.find(this.componentId);
            
            if (component) {
                console.log('Component found', { 
                    component,
                    hasWire: !!component.$wire,
                    hasCall: typeof component.call === 'function',
                    hasWireCall: component.$wire && typeof component.$wire.call === 'function'
                });
                
                // Try $wire.call first (more reliable for nested components)
                if (component.$wire && typeof component.$wire.call === 'function') {
                    console.log('Calling submit via component.$wire.call');
                    component.$wire.call('submit');
                    return;
                }
                
                // Fallback to component.call
                if (typeof component.call === 'function') {
                    console.log('Calling submit via component.call');
                    component.call('submit');
                    return;
                }
            }
            
            // Final fallback: use Livewire dispatch
            console.warn('Component not found or no call method, using dispatch fallback');
            if (typeof Livewire !== 'undefined' && typeof Livewire.dispatch === 'function') {
                Livewire.dispatch('reset-password-submit', { componentId: this.componentId });
            }
        }
    }" x-on:update-field.window="updateField($event.detail.field, $event.detail.value)">
        <form x-on:submit.prevent="submitForm(); return false;" wire:key="reset-password-form-{{ $this->getId() }}" class="!space-y-4">
            @csrf
            <x-form.input 
                name="code" 
                placeholder="Enter Verification code" 
                autocomplete="one-time-code" 
                id="code-field" 
                :tooltip="false"
                x-on:input="updateField('code', $event.target.value)"
                value="{{ $this->form['code'] ?? '' }}"
            />
            @error('form.code')
                <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
            @enderror
            @error('code')
                <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
            @enderror

            <div x-data="passwordStrength('form.password')" class="">
              <x-form.input 
                name="password" 
                x-bind:type="typeof type !== 'undefined' ? type : 'password'" 
                placeholder="Password" 
                autocomplete="new-password" 
                x-on:input="checkStrength($event.target.value); $dispatch('update-field', {field: 'password', value: $event.target.value})"
                :tooltipModal="true" 
                tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols."
                value="{{ $this->form['password'] ?? '' }}"
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
            @error('password')
                <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
            @enderror

            <div x-data="{ type: 'password' }" x-init="type = 'password'" class="">
              <x-form.input 
                name="password_confirmation" 
                x-bind:type="typeof type !== 'undefined' ? type : 'password'" 
                placeholder="Create a password" 
                autocomplete="new-password" 
                :tooltipModal="true" 
                tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols."
                x-on:input="$dispatch('update-field', {field: 'password_confirmation', value: $event.target.value})"
                value="{{ $this->form['password_confirmation'] ?? '' }}"
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
            @error('password_confirmation')
                <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
            @enderror

            @if ($this->resend)
                <div x-data="timer({{ $this->resend }})" x-init="start()" class="">
                    <span>You can request a new code in (<span x-text="formattedTime"></span>)</span>
                </div>
            @else
                <a x-on:click.prevent="
                    const component = Livewire.find(this.componentId);
                    if (component) {
                        if (component.$wire && typeof component.$wire.call === 'function') {
                            component.$wire.call('resendCode');
                        } else if (typeof component.call === 'function') {
                            component.call('resendCode');
                        }
                    }
                "
                    class="inline-block !text-[#FC7361] hover:!text-[#484134] hover:cursor-pointer !mb-2 font-medium transition">
                    Didn't receive the code? Resend Code
                </a>
            @endif

            <div class="flex justify-start items-stretch gap-3">
                <a x-on:click.prevent="$dispatch('openModal', { modalName: 'reset-password' })" href="#"
                    class="w-full border text-center !text-[#FC7361] !border-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] font-medium !py-2.5 !rounded-lg transition">
                    Back
                </a>

                <button type="submit"
                    class="w-full !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

@script
<script>
    (function() {
        const componentId = @js($this->getId());
        
        console.log('ResetPasswordConfirm component script initialized', { componentId });
        
        // Ensure wire:model bindings work correctly by updating them after component mounts
        Livewire.hook('mounted', ({ component }) => {
            if (component && typeof component.getId === 'function' && component.getId() === componentId) {
                console.log('ResetPasswordConfirm component mounted', { 
                    componentId: component.getId(),
                    form: component.$wire?.form,
                    email: component.$wire?.email
                });
            }
        });
        
        // Hook into component updates to debug
        Livewire.hook('morph', ({ el, component }) => {
            if (component && typeof component.getId === 'function' && component.getId() === componentId) {
                console.log('ResetPasswordConfirm component morphed', {
                    form: component.$wire?.form,
                });
            }
        });
        
        // Listen for network requests to debug
        Livewire.hook('request', ({ component, succeed, fail }) => {
            if (component && typeof component.getId === 'function' && component.getId() === componentId) {
                console.log('ResetPasswordConfirm request started', {
                    componentId: component.getId(),
                    payload: component.$wire?.form
                });
                
                succeed(({ snapshot, effects }) => {
                    console.log('ResetPasswordConfirm request succeeded', {
                        snapshot,
                        effects
                    });
                });
                
                fail(({ snapshot, effects }) => {
                    console.error('ResetPasswordConfirm request failed', {
                        snapshot,
                        effects
                    });
                });
            }
        });
    })();
</script>
@endscript
