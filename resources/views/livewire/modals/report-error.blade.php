<div class="w-full text-center space-y-4">
  <div class="text-2xl font-semibold text-red-500">We couldn’t submit your report</div>
  <p class="text-gray max-w-lg mx-auto">
    Something went wrong while sending your message. Please try again in a moment.
  </p>
  <div class="flex justify-center gap-3">
    <x-btn class="!px-6" wire:click.prevent="$dispatch('closeModal')">Close</x-btn>
  </div>
</div>
