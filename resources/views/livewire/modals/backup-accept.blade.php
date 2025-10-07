<div>
  <div class="text-center">
    <x-title class="!mb-6">Two-Factor Authentication Disabled</x-title>
    <div class="!mb-6">
      @include('icons.success')
    </div>
    <div class="!mb-6">
      You have successfully disabled two-factor authentication. For your security, we strongly recommend enabling 2FA again in your account settings as soon as possible.
    </div>
    <div class="">
      <x-btn wire:click.prevent="$dispatch('openModal', { modalName: 'auth' })" class="sm:!max-w-48">Continue</x-btn>
    </div>
  </div>
</div>
