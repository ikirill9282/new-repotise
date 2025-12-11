<div class="relative">

  <h2 class="text-2xl font-bold text-center select-none !mb-6">Forgrot password?</h2>

  <div class="!mb-6 text-center">
    Please enter the email address associated with your account. We'll send you a verification code to reset your password.
  </div>

  <form wire:submit="submit" class="!space-y-4">
    @csrf
    <x-form.input wire:model="form.email" name="email" placeholder="Email Address" :tooltipModal="false" :tooltip="false" />
    
    @if($this->showRecaptchaV2)
      <div class="!mb-4" id="recaptcha-v2-container-reset-password"></div>
      <input type="hidden" wire:model="form.recaptcha_token" name="recaptcha_token" id="recaptcha_token_reset_password">
      @error('form.recaptcha_token')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
      @enderror
    @endif

    <div class="flex justify-start items-stretch gap-3">
      <a wire:click.prevent="$dispatch('openModal', {modalName: 'auth'})" href="#" class="w-full border text-center !text-[#FC7361] !border-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] font-medium !py-2.5 !rounded-lg transition">
        Back to Login
      </a>

      <button type="submit" class="w-full !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
        Send Reset Code
      </button>
    </div>
  </form>
  
  @push('js')
  <script>
    @php
      $recaptchaSiteKey = config('services.recaptcha.site_key');
    @endphp
    
    @if($recaptchaSiteKey)
    // reCAPTCHA v2 callback for password reset
    window.onRecaptchaV2Load = function() {
      Livewire.hook('morph.updated', ({ el, component }) => {
        if (component.showRecaptchaV2 && document.getElementById('recaptcha-v2-container-reset-password') && !document.getElementById('recaptcha-v2-widget-reset-password')) {
          grecaptcha.render('recaptcha-v2-container-reset-password', {
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

</div>