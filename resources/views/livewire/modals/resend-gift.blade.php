@if($errorMessage)
    <div class="text-red-500">{{ $errorMessage }}</div>
@elseif($gift)
    <div class="relative">
        <h2 class="text-2xl font-bold text-center select-none !mb-6">Resend gift email?</h2>
        
        <p class="!mb-6 text-center">
            We'll send the gift email again to <strong>{{ $gift->recipient_email }}</strong>.<br>
            Ask the recipient to also check their spam folder.
        </p>

        <div class="flex justify-start items-stretch gap-3">
            <button 
                wire:click.prevent="$dispatch('closeModal')"
                class="w-full border text-center !text-[#FC7361] !border-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] font-medium !py-2.5 !rounded-lg transition">
                Cancel
            </button>
            <button 
                wire:click="resend"
                class="w-full !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
                Resend email
            </button>
        </div>
    </div>
@endif
