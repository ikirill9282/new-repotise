<div>

  <div class="flex justify-betweeen items-stretch !gap-12 flex-col lg:flex-row">
    <div class="basis-1/2">
      {{-- HEAD --}}
      <div class="!text-2xl !font-semibold !mb-4">Let's Connect</div>
      <div class="max-w-md !mb-10">
        Ready to explore how partnering with TrekGuider can benefit your business? Reach out to our partnership team today!
      </div>


      {{-- FORM --}}
      <div
        x-data="{
          len: 0,
          max: 500,
          setLen(val) {
            this.len = val;
          }
        }"
        class="flex flex-col justify-between items-stretch !gap-3">
        <div class="">
          <x-form.input wire:model="fields.name" :tooltip="false" name="name" placeholder="Company name" />
        </div>

        <div class="">
          <x-form.select 
            wire:model="fields.topic" :tooltip="false"
            label="Select a topic" 
            name="topic"
            :options="[
              'General Inquiry' => 'General Inquiry', 
              'Partnerships' => 'Partnerships',
              'Investing' => 'Investing', 
              'Marketing' => 'Marketing',
              'Collaborations' => 'Collaborations',
              'Other' => 'Other',
            ]" 
          />
        </div>

        <x-form.textarea-counter wire:model="fields.text" :tooltip="false" name="text" placeholder="Text your message"></x-form.textarea-counter>
        
        <div class="!mb-4" id="recaptcha-v2-container-invest"></div>
        <input type="hidden" wire:model="fields.recaptcha_token" name="recaptcha_token" id="recaptcha_token_invest">
        @error('fields.recaptcha_token')
          <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror

        <x-btn wire:click.prevent="submit" class="sm:!w-auto self-center lg:self-start sm:!px-12">Start Partnership</x-btn>
      </div>
    </div>


    <div class="basis-1/2">
      {{-- LOGO --}}
      <div class="w-full">
        <img class="mx-auto" src="{{ asset('assets/img/formImage.png') }}" alt="Investment" />
      </div>
    </div>
  </div>
</div>

@push('js')
<script>
  @php
    $recaptchaSiteKey = config('services.recaptcha.site_key');
  @endphp
  
  @if($recaptchaSiteKey)
  // reCAPTCHA v2 callback for invest form
  window.onRecaptchaV2Load = function() {
    Livewire.hook('morph.updated', ({ el, component }) => {
      if (document.getElementById('recaptcha-v2-container-invest') && !document.getElementById('recaptcha-v2-widget-invest')) {
        grecaptcha.render('recaptcha-v2-container-invest', {
          'sitekey': '{{ $recaptchaSiteKey }}',
          'callback': function(token) {
            @this.set('fields.recaptcha_token', token);
          },
          'expired-callback': function() {
            @this.set('fields.recaptcha_token', null);
          }
        });
      }
    });
  };
  @endif
</script>
@endpush
