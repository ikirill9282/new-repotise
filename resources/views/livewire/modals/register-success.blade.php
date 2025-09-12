<div
    class=""
>
    <div class="text-center">
        @include('icons.success')

        <h2 id="popup-title" class="text-2xl font-semibold !mb-2">Success!</h2>
        <p class="text-gray-600 !mb-6">A confirmation email has been sent to your email address. Please check your inbox and click the link to verify your account.</p>
        
        <button wire:click.prevent="$dispatch('openModal', { modalName: 'auth' })" class="w-full !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
          Sign In
        </button>
    </div>
</div>