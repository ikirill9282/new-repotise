<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Contact {{ $user->getName() }}</div>
  <div class="!mb-6">
    
    @if(empty($contacts))
      <div class="text-center text-lg">The {{ $user->getName() }} has not provided contact details yet.</div>
    @else
      <div class="flex flex-col justify-start items-stretch !gap-3" id="contacts-container" style="display: none;">
        @foreach ($contacts as $contact)
          <div class="flex items-center justify-between !p-3 rounded bg-light copyToClipboard hover:cursor-pointer" data-target="contact1">
            <div class="" data-copyId="contact1">{{ $contact }}</div>
            <div class="">@include('icons.copy')</div>
          </div>
        @endforeach
      </div>
      
      <div id="recaptcha-container" class="!mb-4">
        <div id="recaptcha-v2-container-contact-modal"></div>
        <input type="hidden" wire:model="recaptcha_token" name="recaptcha_token" id="recaptcha_token_contact_modal">
      </div>
    @endif

  </div>

  <div class="text-center">
    @if(!empty($contacts))
      <x-btn wire:click.prevent="showContacts" class="!max-w-[9rem] !inline-block !py-2" id="show-contacts-btn">Show Contacts</x-btn>
    @endif
    <x-btn wire:click.prevent="$dispatch('closeModal')" class="!max-w-[9rem] !inline-block !py-2">Done</x-btn>
  </div>
</div>

@push('js')
<script>
  @php
    $recaptchaSiteKey = config('services.recaptcha.site_key');
  @endphp
  
  @if($recaptchaSiteKey)
  // reCAPTCHA v2 callback for contact modal
  window.recaptchaCallbacks = window.recaptchaCallbacks || [];
  window.recaptchaCallbacks.push(function () {
    Livewire.hook('morph.updated', ({ el, component }) => {
      if (document.getElementById('recaptcha-v2-container-contact-modal') && !document.getElementById('recaptcha-v2-widget-contact-modal')) {
        grecaptcha.render('recaptcha-v2-container-contact-modal', {
          'sitekey': '{{ $recaptchaSiteKey }}',
          'callback': function(token) {
            @this.set('recaptcha_token', token);
          },
          'expired-callback': function() {
            @this.set('recaptcha_token', null);
          }
        });
      }
    });
  });
  
  // Show contacts after successful verification
  Livewire.on('toastSuccess', (message) => {
    if (message[0] && message[0].message && message[0].message.includes('Contacts displayed')) {
      document.getElementById('contacts-container').style.display = 'flex';
      document.getElementById('recaptcha-container').style.display = 'none';
      document.getElementById('show-contacts-btn').style.display = 'none';
    }
  });
  @endif
</script>
@endpush
