<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold !mb-2 mx-auto max-w-xs sm:max-w-none">Are You Sure You Want to Delete Your Account?</div>

  {{-- IMAGE --}}
  <div class="flex justify-center items-center py-5">
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
        label="Current Password"
        placeholder="Enter your current password"
        wire:model.defer="current_password"
        autocomplete="current-password"
      ></x-form.input>
      @error('current_password')
        <p class="text-xs text-error mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div>
      <div class="flex items-end justify-between gap-3 flex-col sm:flex-row">
        <div class="w-full">
          <x-form.input
            label="Email Verification Code"
            placeholder="Enter 6-digit code from email"
            data-input="integer"
            wire:model.defer="verification_code"
            inputmode="numeric"
            class="mb-2"
          ></x-form.input>
          @error('verification_code')
            <p class="text-xs text-error mt-1">{{ $message }}</p>
          @enderror
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
