<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Two-Factor Authentication Enabled!</div>

    {{-- LOGO --}}
    <div class="!mb-2">
      @include('icons.success')
    </div>

    {{-- TEXT --}}
    <div class="mb-4">
      <p>
        Success! 2FA is now active on your account. You will be asked for a verification code during future logins. Remember to keep your Backup Reset Code safe in case you lose access to your authenticator app.
      </p>
    </div>

    {{-- BUTTON --}}
    <div class="max-w-38 mx-auto">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="uppercase">Done</x-btn>
    </div>
</div>
