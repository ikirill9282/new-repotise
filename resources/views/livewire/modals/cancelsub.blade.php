<div class="">
  {{-- HEADER --}}
  <div class="text-2xl font-semibold mb-6">Cancel Subscription?</div>

  {{-- BILLING DATES --}}
  <div class="mb-6 flex flex-col gap-2">
    <p>Your current billing period ends on: <span class="!text-dark font-semibold text-nowrap">[Дата окончания текущего периода].</span></p>
    <p>You are about to cancel your subscription to: <span class="!text-dark font-semibold text-nowrap">[Название продукта/подписки]</span></p>
  </div>

  {{-- RULES --}}
  <div class="mb-6">
    <div class="mb-2">If you cancel now:</div>
    <ul class="!pl-5">
      <li class="!list-disc">You will not be charged for future billing cycles.</li>
      <li class="!list-disc">You will retain access to <span class="!text-dark font-semibold text-nowrap">[Название продукта/подписки]</span> until <span class="!text-dark font-semibold text-nowrap">[Дата окончания текущего периода]</span></li>
    </ul>
  </div>

  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-2 flex-col sm:flex-row">
    <x-btn class="!text-sm sm:!text-base" wire:click.prevent="$dispatch('closeModal')">Keep Subscription</x-btn>
    <x-btn class="!text-sm sm:!text-base" outlined>Yes, Cancel Subscription</x-btn>
  </div>
</div>
