<div class="w-full space-y-4">
  @if (!empty($context))
    <div class="italic p-3 bg-sky-100 rounded text-sky-900">{{ $context }}</div>
  @endif

  @if ($submitted)
    <div class="text-center space-y-4">
      <div class="text-2xl font-semibold text-active">Thanks for letting us know!</div>
      <p class="text-gray max-w-lg mx-auto">
        We received your report and will review the article shortly. Your help keeps our content accurate.
      </p>
      <div class="flex justify-center">
        <x-btn class="!px-6" wire:click.prevent="$dispatch('closeModal')">Close</x-btn>
      </div>
    </div>
  @elseif($failed)
    <div class="text-center space-y-4">
      <div class="text-2xl font-semibold text-red-500">We couldn’t submit your report</div>
      <p class="text-gray max-w-lg mx-auto">
        Something went wrong while sending your message. Please try again in a moment.
      </p>
      <div class="flex justify-center gap-3">
        <x-btn class="!px-6" outlined wire:click.prevent="$set('failed', false)">Back</x-btn>
        <x-btn class="!px-6" wire:click.prevent="$dispatch('closeModal')">Close</x-btn>
      </div>
    </div>
  @else
    <form wire:submit.prevent="submit" class="flex flex-col gap-4">
      <div class="flex flex-col gap-2">
        <label class="text-sm text-gray" for="report-message">What needs to be fixed?</label>
        <textarea
          id="report-message"
          wire:model.defer="form.message"
          rows="4"
          maxlength="1000"
          class="w-full rounded border border-gray/50 p-3 shadow-sm focus:border-active focus:ring-0 resize-y min-h-[150px]"
          placeholder="Describe the mistake you noticed so we can correct it."
        ></textarea>
        @error('form.message')
          <div class="text-sm text-red-500">{{ $message }}</div>
        @enderror
      </div>

      <div class="popUp__donateThank-buttons flex items-center justify-end gap-2">
        <button type="button" @click.prevent="$dispatch('closeModal')" class="popUp__donate-fail-cancelBtn">Cancel</button>
        <button type="submit" class="popUp__edit-contact-btn main-btn" wire:loading.attr="disabled">
          <span wire:loading.remove>Send</span>
          <span wire:loading>Sending...</span>
        </button>
      </div>
    </form>
  @endif
</div>
