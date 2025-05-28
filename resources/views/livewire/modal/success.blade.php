<div
    class="w-full flex items-center justify-center z-20"
>
    <div class="rounded-lg shadow-lg !p-8 w-full text-center !mb-5">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="mx-auto mb-4 text-green-600"
            width="64"
            height="64"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
            >
            <path d="M20 6L9 17l-5-5" />
        </svg>

        <h2 id="popup-title" class="text-2xl font-semibold !mb-2">Success!</h2>
        <p class="text-gray-600 !mb-6">A confirmation email has been sent to your email address. Please check your inbox and click the link to verify your account.</p>
        
        <button wire:click="openAuth" class="w-full !bg-orange-400 hover:!bg-orange-600 text-white font-medium !py-2.5 !rounded-lg transition-colors transition">
          Sign In
        </button>
    </div>
</div>