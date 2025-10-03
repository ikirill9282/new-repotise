<div
    class=""
>
    <div class="text-center">
        <h2 id="popup-title" class="text-2xl font-semibold !mb-2">Password Reset Successful!</h2>

        <div class="">
          @include('icons.success')
        </div>

        <p class="text-gray-600 !mb-6">Your password has been successfully updated. You can now log in using your new password.</p>
        
        <x-btn wire:click.prevent="$dispatch('openModal', { modalName: 'auth' })" class="!max-w-64">
          Sign In
        </x-btn>
    </div>
</div>