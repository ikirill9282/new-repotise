<div id="delete-account-modal">
  <style>
    /* Убеждаемся, что все тултипы за модальным окном находятся ниже по z-index */
    /* Backdrop модального окна (.cartMayBe) имеет z-50, тултипы имеют z-20 */
    /* Но тултипы могут быть видны, если они добавляются после открытия модального окна */
    /* Поэтому мы понижаем z-index всех тултипов, которые находятся за модальным окном */
    
    /* Когда модальное окно открыто, все тултипы за ним должны быть ниже */
    .cartMayBe:not(.hidden) ~ * .tooltip,
    .cartMayBe:not(.hidden) ~ .tooltip,
    body:has(.cartMayBe:not(.hidden)) .tooltip:not(.cartMayBe .tooltip) {
      z-index: 1 !important;
    }
    
    /* Альтернативный подход: понижаем z-index всех тултипов, когда модальное окно видимо */
    body.modal-open .tooltip:not(.cartMayBe .tooltip),
    body:has(.cartMayBe:not(.hidden)) .tooltip:not(.cartMayBe .tooltip) {
      z-index: 1 !important;
    }
    
    /* Самый надежный способ: понижаем z-index всех тултипов с z-20, которые не внутри модального окна */
    .tooltip.z-20:not(.cartMayBe .tooltip),
    [class*="tooltip"][class*="z-20"]:not(.cartMayBe [class*="tooltip"]) {
      z-index: 1 !important;
    }
  </style>
  @push('js')
  <script>
    (function() {
      // Понижаем z-index всех тултипов за модальным окном
      function lowerTooltipsZIndex() {
        const modal = document.querySelector('.cartMayBe');
        if (!modal || modal.classList.contains('hidden')) return;
        
        // Находим все тултипы, которые не внутри модального окна
        const allTooltips = document.querySelectorAll('.tooltip, [class*="tooltip"]');
        allTooltips.forEach(function(tooltip) {
          // Проверяем, находится ли тултип внутри модального окна
          const isInsideModal = modal.contains(tooltip);
          if (!isInsideModal) {
            // Понижаем z-index тултипов за модальным окном
            const computedStyle = window.getComputedStyle(tooltip);
            const zIndex = parseInt(computedStyle.zIndex) || 0;
            if (zIndex >= 20) {
              tooltip.style.setProperty('z-index', '1', 'important');
            }
          }
        });
      }
      
      // Вызываем при открытии модального окна
      if (window.Livewire) {
        Livewire.hook('morph.updated', function() {
          setTimeout(lowerTooltipsZIndex, 50);
        });
      }
      
      // Также вызываем периодически
      const intervalId = setInterval(function() {
        const modal = document.querySelector('.cartMayBe');
        if (modal && !modal.classList.contains('hidden')) {
          lowerTooltipsZIndex();
        }
      }, 100);
    })();
  </script>
  @endpush
  {{-- HEADER --}}
  <div class="text-2xl font-semibold !mb-2 mx-auto max-w-xs sm:max-w-none" data-no-tooltip="true">Are You Sure You Want to Delete Your Account?</div>

  {{-- IMAGE --}}
  <div class="flex justify-center items-center py-5" data-no-tooltip="true">
    @include('icons.warning', ['main' => '#FF2C0C', 'second' => '#ffffff'])
  </div>

  {{-- DESCRIPTION --}}
  <div class="mb-4">
    <ul class="!pl-4 group">
      <li class="!list-disc">This action will schedule your account for permanent deletion.</li>
      <li class="!list-disc">You can cancel this deletion by logging back in within the next <b>30 days</b>.</li>
      <li class="!list-disc"><b>After 30 days:</b> All your personal data, profile information, purchase history, uploaded products, and articles will be <b>permanently erased</b> and cannot be recovered.</li>
      <li class="!list-disc">Any eligible balance will be processed for withdrawal according to our <x-link class="!border-none group-has-[a]:!text-active" href="{{ route('policies') }}">Policies</x-link> (minimum amounts and verification status apply).</li>
    </ul>
  </div>


  {{-- INPUTS --}}
  <form wire:submit.prevent="confirmDeletion" class="mb-5 space-y-4">
    <div>
      <x-form.input
        type="password"
        class="mb-1"
        name="current_password"
        label="Current Password"
        placeholder="Enter your current password"
        wire:model.defer="current_password"
        autocomplete="current-password"
        :tooltip="false"
      ></x-form.input>
    </div>

    <div>
      <div class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
        <div class="w-full min-w-0">
          <x-form.input
            label="Email Verification Code"
            placeholder="Enter 6-digit code from email"
            name="verification_code"
            data-input="integer"
            wire:model.defer="verification_code"
            inputmode="numeric"
            autocomplete="one-time-code"
            class="mb-2"
            :tooltip="false"
          ></x-form.input>
        </div>

        <x-btn
          type="button"
          class="!text-sm sm:!text-base !w-auto text-nowrap"
          outlined
          wire:click.prevent="sendVerificationCode"
          wire:target="sendVerificationCode"
          wire:loading.attr="disabled"
          :disabled="$this->resendDisabled"
        >
          <span wire:loading.remove wire:target="sendVerificationCode">Send Verification Code</span>
          <span wire:loading wire:target="sendVerificationCode">Sending...</span>
        </x-btn>
      </div>

      @if($this->resendDisabled && $this->resendSeconds)
        <p class="text-xs text-muted mt-2">You can request a new code in {{ $this->resendSeconds }}s.</p>
      @endif
    </div>
  
    {{-- BUTTONS --}}
    <div class="flex justify-center items-center gap-1.5 sm:gap-3">
      <x-btn class="!text-sm sm:!text-base !px-2 !w-auto sm:!px-12" type="button" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
      <x-btn type="submit" class="!text-sm sm:!text-base !px-2 !grow" wire:target="confirmDeletion" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="confirmDeletion">Confirm & Schedule Deletion</span>
        <span wire:loading wire:target="confirmDeletion">Processing...</span>
      </x-btn>
    </div>
  </form>
</div>
