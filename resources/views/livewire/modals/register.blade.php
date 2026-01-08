<div class="relative">
  <h2 class="text-2xl font-bold text-gray-900 !mb-6 text-center select-none">Welcome!</h2>
  
  <form wire:submit.prevent="attempt" class="!space-y-4 !mb-10">
    @csrf
    
    <x-form.input wire:model="form.email" name="email" type="email" autocomplete="email" :tooltipModal="true" />

    <div x-data="passwordStrength('form.password')" class="">
      <x-form.input wire:model="form.password" name="password" x-bind:type="typeof type !== 'undefined' ? type : 'password'" autocomplete="new-password" placeholder="Password" x-on:input="checkStrength($event.target.value)" :tooltipModal="true" tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols.">
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

    <div x-data="{ type: 'password' }" x-init="type = 'password'" class="">
      <x-form.input wire:model="form.repeat_password" name="repeat_password" x-bind:type="typeof type !== 'undefined' ? type : 'password'" autocomplete="new-password" placeholder="Create a password" :tooltipModal="true" tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols.">
        <x-slot name="icon">
          <div x-on:click="() => type = (type == 'password') ? 'text' : 'password' " class="absolute top-1/2 right-9 translate-y-[-50%] hover:cursor-pointer">
            <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
          </div>
        </x-slot>
      </x-form.input>
    </div>

    <div class="!mb-10">
      <x-form.checkbox wire:model="form.as_seller" name="as_seller" label="Sign up as a seller" />
    </div>

    <div class="flex justify-between items-center !gap-2">
      <x-btn type="button" wire:click.prevent="$dispatch('openModalAfterClose', ['auth'])" class="basis-1/3" gray>Cancel</x-btn>
      <x-btn type="submit" class="basis-2/3" wire:loading.attr="disabled" id="register-submit-btn">
        <span wire:loading.remove>Sign Up</span>
        <span wire:loading>Signing Up...</span>
      </x-btn>
    </div>

    <div class="flex items-center justify-center group text-gray">
      Already have an account? <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'auth' })" href="#" class="!border-0 !inline-bliock !p-0 ml-1 group-has-[a]:!text-active">Sign In</x-link>
    </div>
  </form>

  <div class="flex justify-center items-center !gap-2 !mb-6">
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
    <div class="text-gray shrink-0 text-sm">Other log in options.</div>
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
  </div>

  <div class="flex justify-between items-center !gap-2 text-gray !mb-6">
    <div wire:click.prevent="googleAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/google.svg') }}" alt="Google"></div>
      <div class="transition group-hover:text-active !mt-0.5">Google</div>
    </div>
    <div wire:click.prevent="fbAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/facebook.svg') }}" alt="Facebook"></div>
      <div class="transition group-hover:text-active !mt-0.5">Facebook</div>
    </div>
    <div wire:click.prevent="xAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/xai.svg') }}" alt="XAI"></div>
      <div class="transition group-hover:text-active !mt-0.5">X (Twitter)</div>
    </div>
  </div>

  <div class="group text-sm text-gray">
    By clicking ‘‘Sign Up,’’ you agree to our <x-link href="/policies/terms-and-conditions" target="_blank" class="!border-0 group-has-[a]:!text-active">Terms of Service</x-link>, <x-link href="/policies/privacy-policy" target="_blank" class="!border-0 group-has-[a]:!text-active">Privacy Policy</x-link>, and <x-link href="/policies" target="_blank" class="!border-0 group-has-[a]:!text-active">Other Terms</x-link>.
  </div>
</div>

@push('js')
<script>
  function passwordStrength(wireModel) {
    return {
      type: 'password',
      password: '',
      strength: 'weak',
      strengthText: 'Weak',
      
      init() {
        // Initial check if value exists
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