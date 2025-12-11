<div class="relative">

  <h2 class="text-2xl font-bold text-center select-none !mb-6">Use Backup Code</h2>
  <p class="!mb-4">Please enter your Backup Reset Code to regain access to your account.</p>
  <form wire:submit="submit" class="!space-y-4">
    @csrf

    <x-form.input wire:model="form.code" name="code" placeholder="Backup Code" autocomplete="one-time-code" :tooltipModal="true" tooltipText="Enter the Backup Reset Code you saved when you enabled Two-Factor Authentication." />
    
    <div class="!mb-4" id="recaptcha-v2-container-backup"></div>
    <input type="hidden" wire:model="form.recaptcha_token" name="recaptcha_token" id="recaptcha_token_backup">
    @error('form.recaptcha_token')
      <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror

    <div class="flex justify-start items-stretch gap-3 group">
      <div class="basis-1/4">
        <x-btn wire:click.prevent="$dispatch('openModal', {modalName: 'auth'})" href="#" class="group-has-[a]:!text-black group-has-[a]:!bg-transparent">
          Back
        </x-btn>
      </div>

      <div class="basis-3/4">
        <x-btn wire:click.prevent="attempt" class="">
          Reset two-factor authentication
        </x-btn>
      </div>
    </div>
  </form>
  
  @push('js')
  <script>
    @php
      $recaptchaSiteKey = config('services.recaptcha.site_key');
    @endphp
    
    @if($recaptchaSiteKey)
    // reCAPTCHA v2 callback for 2FA reset
    window.onRecaptchaV2Load = function() {
      Livewire.hook('morph.updated', ({ el, component }) => {
        if (document.getElementById('recaptcha-v2-container-backup') && !document.getElementById('recaptcha-v2-widget-backup')) {
          grecaptcha.render('recaptcha-v2-container-backup', {
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