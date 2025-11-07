@once
  @push('head')
    <script src="https://js.stripe.com/v3/"></script>
  @endpush
@endonce

<div class="w-full">
  <div class="pb-6 mb-4 border-b-1 border-gray/30">
    <div class="text-2xl font-semibold mb-3">Add Payment Method</div>
    <p class="text-gray">
      Securely save a new card to your TrekGuider account. Payments are processed by Stripe and we never store your full card details.
    </p>
  </div>

  <div wire:ignore>
    <form id="pm-form" class="flex flex-col gap-4">
      <div>
        <x-form.input
          id="pm-cardholder-name"
          label="Cardholder Name"
          placeholder="Name on card"
          :tooltip="false"
        />
      </div>

      <div class="flex flex-col gap-2">
        <label class="text-sm text-gray">Card Details</label>
        <div id="pm-payment-element" class="w-full px-3 py-3 rounded-lg border border-gray/40 bg-white shadow-xs"></div>
        <p id="pm-errors" class="text-sm text-red-500 hidden"></p>
      </div>

      <div class="flex justify-center items-center gap-3 pt-2">
        <x-btn class="!text-sm sm:!text-base !w-auto sm:!px-12" type="button" wire:click.prevent="$dispatch('payment-method-close'); $dispatch('closeModal')" outlined>Cancel</x-btn>
        <x-btn class="!text-sm sm:!text-base !grow" id="pm-submit" type="submit">
          Save Payment Method
        </x-btn>
      </div>
    </form>
  </div>
</div>

@script
  <script>
    (() => {
      const stripeKey = @js($publishableKey);
      const clientSecret = @js($clientSecret);

      if (!stripeKey || !clientSecret) {
        console.error('Stripe configuration is missing.');
        return;
      }

      let stripeInstance = null;
      let elementsInstance = null;
      let paymentElement = null;
      let submitButton = null;
      let formListener = null;
      let initialized = false;

      const disableButton = () => {
        if (submitButton) {
          submitButton.setAttribute('disabled', 'disabled');
          submitButton.classList.add('opacity-60', 'cursor-not-allowed');
        }
      };

      const enableButton = () => {
        if (submitButton) {
          submitButton.removeAttribute('disabled');
          submitButton.classList.remove('opacity-60', 'cursor-not-allowed');
        }
      };

      const clearError = (block) => {
        if (block) {
          block.textContent = '';
          block.classList.add('hidden');
        }
      };

      const showError = (block, message) => {
        if (block) {
          block.textContent = message || 'Something went wrong.';
          block.classList.remove('hidden');
        }
      };

      const reset = () => {
        initialized = false;

        if (formListener && window.pmFormElement) {
          window.pmFormElement.removeEventListener('submit', formListener);
        }

        if (paymentElement) {
          paymentElement.unmount();
        }

        const container = document.getElementById('pm-payment-element');
        if (container && container.dataset.initialized) {
          delete container.dataset.initialized;
        }

        stripeInstance = null;
        elementsInstance = null;
        paymentElement = null;
        submitButton = null;
        formListener = null;
        window.pmFormElement = null;
      };

      const init = () => {
        const form = document.getElementById('pm-form');
        const container = document.getElementById('pm-payment-element');
        const cardholderInput = document.getElementById('pm-cardholder-name');
        const errorBlock = document.getElementById('pm-errors');

        if (!form || !container) {
          setTimeout(init, 50);
          return;
        }

        if (initialized) {
          return;
        }

        if (typeof window.Stripe !== 'function') {
          setTimeout(init, 50);
          return;
        }

        initialized = true;
        submitButton = document.getElementById('pm-submit');
        window.pmFormElement = form;

        stripeInstance = Stripe(stripeKey);
        elementsInstance = stripeInstance.elements({ clientSecret });
        paymentElement = elementsInstance.create('payment');
        paymentElement.mount(container);

        formListener = async (event) => {
          event.preventDefault();

          disableButton();
          clearError(errorBlock);

          const { error, setupIntent } = await stripeInstance.confirmCardSetup(clientSecret, {
            elements: elementsInstance,
            confirmParams: {
              payment_method_data: {
                billing_details: {
                  name: (cardholderInput?.value ?? '').trim() || undefined,
                },
              },
            },
            redirect: 'if_required',
          });

          if (error) {
            showError(errorBlock, error.message);
            enableButton();
            return;
          }

          if (!setupIntent?.payment_method) {
            showError(errorBlock, 'Unable to add payment method. Please try again.');
            enableButton();
            return;
          }

          $wire.attachPaymentMethod(setupIntent.payment_method);
        };

        form.addEventListener('submit', formListener);
      };

      Livewire.on('payment-method-open', () => {
        reset();
        setTimeout(init, 20);
      });

      Livewire.on('payment-method-close', reset);

      Livewire.on('payment-method-add-failed', enableButton);

      document.addEventListener('livewire:load', () => {
        Livewire.hook('morphed', () => {
          if (initialized) {
            setTimeout(init, 20);
          }
        });
      });

      window.addEventListener('modalClosing', reset);
    })();
  </script>
@endscript
