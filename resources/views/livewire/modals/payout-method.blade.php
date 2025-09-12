<div>
  <div class="pb-6 mb-4 border-b-1 border-gray/30">
    {{-- HEADER --}}
    <div class="text-2xl font-semibold mb-3">Verify New Payout Method</div>
    
    {{-- DESCRIPTION --}}
    <div class="">
      For your security, we've sent a 6-digit verification code to your registered email address. Please enter the code below to confirm adding this payout method. The code is valid for 30 minutes.
    </div>
  </div>

  <div class="flex justify-start items-end gap-3 mb-5">
    <x-form.input label="Email Verification Code" placeholder="Enter 6-digit code" class="grow" data-input="integer"></x-form.input>
    <x-btn outlined class="!w-auto text-nowrap py-3">Resend Code</x-btn>
  </div>


  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="!text-sm sm:!text-base !w-auto sm:!px-12" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!text-sm sm:!text-base !grow">Enable 2FA</x-btn>
  </div>
</div>
