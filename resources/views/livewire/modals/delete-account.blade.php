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
  <div class="mb-5">
    <x-form.input class="mb-4" label="Current Password" placeholder="Enter your current password"></x-form.input>
    
    <div class="flex items-end justify-between gap-3 flex-col sm:flex-row">
      <x-form.input label="Email Verification Code" placeholder="Enter 6-digit code from email" data-input="integer"></x-form.input>
      <x-btn class="!text-sm sm:!text-base !w-auto text-nowrap" outlined>Send Verification Code</x-btn>
    </div>
  </div>


  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-1.5 sm:gap-3">
    <x-btn class="!text-sm sm:!text-base !px-2 !w-auto sm:!px-12" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!text-sm sm:!text-base !px-2 !grow">Confirm & Schedule Deletion</x-btn>
  </div>
</div>
