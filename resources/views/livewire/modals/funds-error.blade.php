<div class="text-center">

    {{-- HEADER --}}
    <div class="text-2xl font-semibold">Payment Initiated Successfully</div>

    {{-- LOGO --}}
    <div class="flex justify-center items-center !py-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="!inline-block" width="144" height="143" viewBox="0 0 144 143" fill="none">
          <path d="M72.0004 142.999C32.5749 142.999 0.5 110.924 0.5 71.499C0.5 32.0734 32.5749 -0.00146484 72.0004 -0.00146484C111.425 -0.00146484 143.5 32.0734 143.5 71.499C143.5 110.924 111.425 142.999 72.0004 142.999ZM72.0004 2.94656C34.2001 2.94656 3.44775 33.6992 3.44775 71.4992C3.44775 109.299 34.2001 140.051 72.0004 140.051C109.8 140.051 140.552 109.299 140.552 71.4992C140.552 33.6992 109.8 2.94656 72.0004 2.94656Z" fill="#FF270E"/>
          <path d="M104.016 51.0951L92.4017 39.4814L71.998 59.8851L51.5941 39.4814L39.9805 51.0951L60.3841 71.499L39.9805 91.9029L51.5941 103.517L71.998 83.1126L92.4017 103.517L104.016 91.9029L83.6116 71.499L104.016 51.0951Z" fill="#FF270E"/>
          <script xmlns=""/>
        </svg>
    </div>

    {{-- TEXT --}}
    <div class="mb-4">
      <p>
        Your payment was successful! The funds should now be reflected in your available balance.
      </p>
    </div>

    {{-- BUTTON --}}
    <div class="mx-auto max-w-2xs">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="uppercase">Done</x-btn>
    </div>
</div>