<div class="relative">
  <h2 class="text-2xl font-bold text-gray-900 !mb-6 text-center select-none">Welcome!</h2>
  
  <form wire:submit.prevent="attempt" class="!space-y-4 !mb-10">
    @csrf

    {{-- STEP 1 --}}
    @if($this->step == 1)

      <div class="!mb-6">
        <x-form.input wire:model="form.email" name="email" type="email" placeholder="Email" :tooltipModal="true"></x-form.input>
      </div>

    {{-- STEP 2 --}}
    @elseif ($this->step == 2)

      <div class="">
        <x-form.input wire:model="form.email" name="email" type="email" placeholder="Email" :tooltipModal="true" autocomplete="one-time-code"></x-form.input>
      </div>
      
      <div x-data="{ type: 'password' }" class="">
        <x-form.input wire:model="form.password" name="password" x-bind:type="type" placeholder="Password" :tooltip="false">
          <x-slot name="icon">
            <div x-on:click="() => type = (type == 'password') ? 'text' : 'password' " class="absolute top-1/2 right-3 translate-y-[-50%] hover:cursor-pointer">
              <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
            </div>
          </x-slot>
        </x-form.input>
      </div>
      
      @if($this->getUser()?->twofa)
        <div class="!mb-3">
          <x-form.input wire:model="form.2fa" name="2fa" placeholder="Authenticator App Code" />
        </div>

        <div class="">
          <x-form.checkbox wire:model="form.backup" label="Use Backup Code" />
        </div>
      @endif
      
      @if($this->showRecaptchaV2)
        <div class="!mb-4" id="recaptcha-v2-container"></div>
        <input type="hidden" wire:model="form.recaptcha_token" name="recaptcha_token" id="recaptcha_token">
        @error('form.recaptcha_token')
          <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
      @endif

    @endif

    <div class="flex justify-between items-center !gap-2">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="basis-1/3" gray>Cancel</x-btn>
      <x-btn wire:click.prevent="attempt" class="basis-2/3" id="login-submit-btn">Continue</x-btn>
    </div>

    <div class="flex items-center justify-center">
      <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'reset-password' })" href="#" class="!border-0">Forgot password?</x-link>
    </div>
  </form>

  <div class="flex justify-center items-center !gap-2 !mb-6">
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
    <div class="text-gray shrink-0 text-sm">Other log in options.</div>
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
  </div>

  <div class="flex justify-between items-center !gap-2 text-gray">
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
</div>

@push('js')
<script>
  @php
    $recaptchaSiteKey = config('services.recaptcha.site_key');
  @endphp
  
  @if($recaptchaSiteKey)
  // reCAPTCHA v3 for login
  document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[wire\\:submit\\.prevent="attempt"]');
    const submitBtn = document.getElementById('login-submit-btn');
    
    if (loginForm && submitBtn && typeof grecaptcha !== 'undefined') {
      loginForm.addEventListener('submit', function(e) {
        const step = @js($this->step);
        const showRecaptchaV2 = @js($this->showRecaptchaV2 ?? false);
        
        if (step === 2 && !showRecaptchaV2) {
          e.preventDefault();
          
          grecaptcha.ready(function() {
            grecaptcha.execute('{{ $recaptchaSiteKey }}', {action: 'login'}).then(function(token) {
              @this.set('recaptcha_token', token);
              @this.call('attempt');
            });
          });
        }
      });
    }
  });
  
  // reCAPTCHA v2 callback
  window.onRecaptchaV2Load = function() {
    Livewire.hook('morph.updated', ({ el, component }) => {
      if (component.showRecaptchaV2 && document.getElementById('recaptcha-v2-container') && !document.getElementById('recaptcha-v2-widget')) {
        grecaptcha.render('recaptcha-v2-container', {
          'sitekey': '{{ $recaptchaSiteKey }}',
          'callback': function(token) {
            @this.set('form.recaptcha_token', token);
          },
          'expired-callback': function() {
            @this.set('form.recaptcha_token', null);
          }
        });
      }
    });
  };
  @endif
</script>
@endpush
