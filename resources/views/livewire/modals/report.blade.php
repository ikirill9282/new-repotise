<div class="w-full space-y-4">
  @if ($submitted)
    <div class="text-center space-y-6">
      <div class="text-2xl font-semibold text-black">Thanks! We'll look into it.</div>
      <div class="flex justify-center">
        @include('icons.success', ['width' => 144, 'height' => 144])
      </div>
      <div class="flex justify-center">
        <button type="button" wire:click.prevent="$dispatch('closeModal')" class=" main-btn" style="background-color: #FF7F66;">
          Done
        </button>
      </div>
    </div>
  @elseif($failed)
    <div class="text-center space-y-4">
      <div class="text-2xl font-semibold text-red-500">Error</div>
      <p class="text-gray max-w-lg mx-auto mb-6">
        @if(empty($form['message']) || mb_strlen(trim($form['message'] ?? ''), 'UTF-8') < 10)
          Please describe the error you found. Your message must be at least 10 characters long.
        @else
          Something went wrong while sending your message. Please try again in a moment.
        @endif
      </p>
      <div class="flex justify-center gap-3">
        <x-btn class="!px-6" wire:click.prevent="$set('failed', false)">Back</x-btn>
        <x-btn class="!px-6" wire:click.prevent="$dispatch('closeModal')">Close</x-btn>
      </div>
    </div>
  @else
    <form wire:submit.prevent="submit" class="flex flex-col gap-4">
      <div class="flex flex-col gap-2">
        <label class="text-base font-semibold text-black title-popup-question" for="report-message">Found an error? Let us know!</label>
        <div class="relative">
          <textarea
            id="report-message"
            wire:model="form.message"
            rows="4"
            maxlength="200"
            class="w-full rounded border border-gray/50 p-3 shadow-sm focus:border-active focus:ring-0 resize-y min-h-[150px]"
            placeholder="Briefly describe the error here..."
            x-on:input="document.getElementById('char-count-text').textContent = $el.value.length"
          ></textarea>
          <div class="absolute bottom-3 right-3 text-sm text-gray pointer-events-none">
            <span id="char-count-text">{{ mb_strlen($form['message'] ?? '', 'UTF-8') }}</span>/200
          </div>
        </div>
        @error('form.message')
          <div class="text-sm text-red-500">{{ $message }}</div>
        @enderror
      </div>

      <div class="popUp__donateThank-buttons flex items-center justify-center gap-2">
        <button type="submit" class="popUp__edit-contact-btn main-btn" wire:loading.attr="disabled">
          <span wire:loading.remove>Submit</span>
          <span wire:loading>Submitting...</span>
        </button>
      </div>
    </form>
  @endif
</div>
