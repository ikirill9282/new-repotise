<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold !mb-2">Subscription Canceled</div>

    {{-- LOGO --}}
    <div class="!mb-2">
        <svg class="inline-block" xmlns="http://www.w3.org/2000/svg" width="202" height="202" viewBox="0 0 202 202" fill="none">
            <path
                d="M151.499 151.499C179.389 123.608 179.389 78.3896 151.499 50.4994C123.609 22.6092 78.3898 22.6092 50.4996 50.4994C22.6095 78.3896 22.6095 123.608 50.4996 151.499C78.3898 179.389 123.609 179.389 151.499 151.499Z"
                fill="#3FC700" />
            <path
                d="M131.04 67.6348L88.0038 110.671L70.9656 93.6325L59.1211 105.477L88.0038 134.36L142.885 79.4792L131.04 67.6348Z"
                fill="white" />
            <script xmlns="" />
        </svg>
    </div>

    {{-- TEXT --}}
    <div class="mb-4">
      <p>
        Your subscription to <span class="!text-dark font-semibold text-nowrap">[Название продукта/подписки]</span> has been successfully canceled. You will not be charged again. You will continue to have access until <span class="!text-dark font-semibold text-nowrap">[Дата окончания текущего периода]</span>
      </p>
    </div>

    {{-- BUTTON --}}
    <div class="mx-auto max-w-2xs">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="uppercase">Done</x-btn>
    </div>
</div>
