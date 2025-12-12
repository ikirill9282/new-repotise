@if($errorMessage)
    <div class="text-red-500">{{ $errorMessage }}</div>
@elseif($gift)
    <div class="relative">
        <h2 class="text-2xl font-bold text-center select-none !mb-6">Change recipient email</h2>
        
        <p class="!mb-4 text-center">
            Update the recipient's email if there was a typo or they no longer use the old address.<br>
            The gift itself won't change – only where we send it.
        </p>

        <form wire:submit="save" class="!space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2">New recipient email</label>
                <x-form.input 
                    wire:model="recipient_email"
                    name="recipient_email"
                    type="email"
                    placeholder="Recipient email"
                    :tooltip="false"
                />
                @error('recipient_email')
                    <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <p class="text-xs text-gray-500 !mb-4">
                After saving, we'll send the gift email to the new address.
            </p>

            <div class="flex justify-start items-stretch gap-3">
                <button 
                    wire:click.prevent="$dispatch('closeModal')"
                    type="button"
                    class="w-full border text-center !text-[#FC7361] !border-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] font-medium !py-2.5 !rounded-lg transition">
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="w-full !bg-[#FC7361] hover:!bg-[#484134] text-white font-medium !py-2.5 !rounded-lg transition">
                    Save & send to new email
                </button>
            </div>
        </form>
    </div>
@endif
