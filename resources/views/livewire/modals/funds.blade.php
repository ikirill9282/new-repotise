<div>
  @once
    @push('head')
      <script src="https://js.stripe.com/v3/"></script>
    @endpush
  @endonce

  {{-- HEADER --}}
  <div class="text-2xl font-semibold mb-2">Add Funds to Your Balance</div>
  <div class="text-sm text-gray pb-6 mb-4 border-b-1 border-gray/30">
    Check this box to add the payment processing fee to your transaction amount. This ensures the full amount you entered above is added to your balance. If unchecked, the fee will be deducted from the amount you add.
  </div>

  {{-- FORM --}}
  <form class="flex flex-col gap-4 pb-6 mb-4 border-b-1 border-gray/30">
    <x-form.input 
      type="number"
      step="0.01"
      min="0"
      label="Amount"
      placeholder="0.00"
      :tooltip="false"
      inputClass="!text-base"
      wire:model.live="amount"
    />

    <x-form.checkbox 
      wire:model.live="coverFees"
      :tooltip="false"
      label="Cover Processing Fees ({{ rtrim(rtrim(number_format($processingPercent, 2, '.', ''), '0'), '.') }}% + ${{ number_format($processingFlat, 2) }})"
    />

    {{-- PAYMENT METHODS TITLE --}}
    <div class="text-2xl font-semibold mt-2">
      Choose Payment Method
    </div>

    {{-- PAYMENT METHODS --}}
    <div class="flex flex-col justify-start items-start gap-2.5 group">
      @foreach($paymentMethods as $method)
        <x-form.payment-method
          wire:model="selectedPaymentMethod"
          name="funds_payment_method" 
          :value="$method['id']"
          :tooltip="false"
          :editor="false"
          :icons="false"
          :brand="$method['label']"
          :last4="$method['last4']"
          class="bg-transparent group-has-[input]:!px-0"
        />
      @endforeach

      {{-- Always allow paying with a new card (Stripe form is embedded below) --}}
      <x-form.payment-method
        wire:model="selectedPaymentMethod"
        name="funds_payment_method"
        value="new"
        :tooltip="false"
        :editor="false"
        :icons="false"
        brand="New Card"
        last4=""
        class="bg-transparent group-has-[input]:!px-0"
      />
    </div>

    {{-- NEW PAYMENT METHOD (Stripe Elements) --}}
    @if($selectedPaymentMethod === 'new' && $setupIntentSecret)
      <div class="mt-2">
        <x-form.input
          id="funds-cardholder-name"
          label="Cardholder Name (optional)"
          placeholder="Name on card"
          :tooltip="false"
        />
      </div>
      <div wire:ignore class="mt-2">
        <label class="text-sm text-gray">Card Details</label>
        <div id="funds-payment-element" class="w-full px-3 py-3 rounded-lg border border-gray/40 bg-white shadow-xs"></div>
        <p id="funds-errors" class="text-sm text-red-500 hidden mt-2"></p>
      </div>
    @endif
  </form>

  {{-- TOTAL --}}
  <div class="w-full flex flex-col items-stretch gap-2 mb-4">
    <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
      <div class="">Amount to add:</div>
      <div class="text-dark font-semibold">{{ currency($summary['credited']) }}</div>
    </div>
    <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
      <div class="">Processing Fee:</div>
      <div class="text-dark font-semibold">{{ currency($summary['processing_fee']) }}</div>
    </div>
    <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
      <div class="">Total Charge:</div>
      <div class="text-dark font-semibold">{{ currency($summary['total_charge']) }}</div>
    </div>
    <div class="text-xs text-gray">
      @if($coverFees)
        {{ currency($summary['processing_fee']) }} will be charged on top of your amount so {{ currency($summary['amount']) }} is credited to your balance.
      @else
        {{ currency($summary['processing_fee']) }} will be deducted from the amount you add, so {{ currency($summary['credited']) }} is credited to your balance.
      @endif
    </div>
  </div>

  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3 max-w-xl mx-auto">
    <x-btn 
      class="!text-sm sm:!text-base !w-auto !px-6" 
      wire:click.prevent="$dispatch('closeModal')" 
      outlined
    >
      Cancel
    </x-btn>
    <x-btn 
      class="!text-sm sm:!text-base !grow"
      wire:click="submit"
      wire:loading.attr="disabled"
      id="funds-submit-btn"
    >
      <span wire:loading.remove wire:target="submit">Add Funds</span>
      <span wire:loading wire:target="submit">Processing...</span>
    </x-btn>
  </div>
</div>

@if($publishableKey)
  @script
    <script>
      (() => {
        const stripeKey = @js($publishableKey);
        const componentId = @js($this->getId());
        let setupIntentSecret = @js($setupIntentSecret);

        let stripeInstance = null;
        let elementsInstance = null;
        let paymentElement = null;
        let initialized = false;

        const ensureStripeLoaded = () => {
          return new Promise((resolve) => {
            if (typeof window.Stripe !== 'undefined') {
              resolve();
              return;
            }
            const existingScript = document.querySelector('script[src*="js.stripe.com"]');
            if (existingScript) {
              const checkInterval = setInterval(() => {
                if (typeof window.Stripe !== 'undefined') {
                  clearInterval(checkInterval);
                  resolve();
                }
              }, 50);
              setTimeout(() => {
                clearInterval(checkInterval);
                resolve();
              }, 5000);
            } else {
              const script = document.createElement('script');
              script.src = 'https://js.stripe.com/v3/';
              script.async = true;
              script.onload = () => resolve();
              script.onerror = () => resolve();
              document.head.appendChild(script);
            }
          });
        };

        const showError = (message) => {
          const el = document.getElementById('funds-errors');
          if (!el) return;
          el.textContent = message || 'Something went wrong.';
          el.classList.remove('hidden');
        };

        const clearError = () => {
          const el = document.getElementById('funds-errors');
          if (!el) return;
          el.textContent = '';
          el.classList.add('hidden');
        };

        const resetStripeElements = () => {
          if (paymentElement) {
            try { paymentElement.unmount(); } catch (e) {}
          }
          paymentElement = null;
          elementsInstance = null;
          stripeInstance = null;
          initialized = false;
          const container = document.getElementById('funds-payment-element');
          if (container) {
            delete container.dataset.initialized;
          }
        };

        const initStripeElements = () => {
          const container = document.getElementById('funds-payment-element');
          if (!container || !setupIntentSecret) return;
          if (container.dataset.initialized === 'true') return;

          ensureStripeLoaded().then(() => {
            if (typeof window.Stripe === 'undefined') {
              showError('Payment library failed to load. Please refresh the page.');
              return;
            }

            try {
              stripeInstance = Stripe(stripeKey);
              elementsInstance = stripeInstance.elements({
                clientSecret: setupIntentSecret,
                appearance: { theme: 'stripe' },
              });
              paymentElement = elementsInstance.create('payment');
              paymentElement.mount(container);
              container.dataset.initialized = 'true';
              initialized = true;
            } catch (error) {
              showError('Failed to initialize payment form. Please refresh the page.');
            }
          });
        };

        const setButtonDisabled = (disabled) => {
          const btn = document.getElementById('funds-submit-btn');
          if (!btn) return;
          if (disabled) {
            btn.setAttribute('disabled', 'disabled');
            btn.classList.add('opacity-60', 'cursor-not-allowed');
          } else {
            btn.removeAttribute('disabled');
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
          }
        };

        // Called by PHP when selectedPaymentMethod === 'new' and user clicks "Add Funds"
        const handleCheckResult = async (event) => {
          const payload = Array.isArray(event) ? (event[0] || event) : event;
          if (!payload || payload.action !== 'create') return;

          clearError();

          if (!stripeInstance || !elementsInstance || !initialized) {
            initStripeElements();
          }

          // Wait a tick if init just started
          await new Promise((r) => setTimeout(r, 50));

          if (!stripeInstance || !elementsInstance) {
            showError('Payment form is not ready. Please try again.');
            return;
          }

          setButtonDisabled(true);

          const cardholderName = (document.getElementById('funds-cardholder-name')?.value || '').trim();

          const setupOptions = {
            elements: elementsInstance,
            redirect: 'if_required',
          };

          if (cardholderName) {
            setupOptions.confirmParams = {
              payment_method_data: {
                billing_details: { name: cardholderName },
              },
            };
          }

          try {
            const { error, setupIntent } = await stripeInstance.confirmSetup(setupOptions);
            if (error) {
              showError(error.message);
              setButtonDisabled(false);
              return;
            }

            if (!setupIntent?.payment_method) {
              showError('Unable to save card. Please try again.');
              setButtonDisabled(false);
              return;
            }

            const component = Livewire.find(componentId);
            if (!component) {
              showError('Something went wrong. Please try again.');
              setButtonDisabled(false);
              return;
            }

            // Server will create PaymentIntent using this payment method id
            component.call('processWithPaymentMethod', setupIntent.payment_method);
          } catch (e) {
            showError('Unable to process card. Please try again.');
            setButtonDisabled(false);
          }
        };

        // Keep secret in sync if backend refreshes it
        document.addEventListener('livewire:init', () => {
          Livewire.on('setup-intent-updated', (event) => {
            const secret = Array.isArray(event) ? (event[0] || event) : event;
            setupIntentSecret = secret;
            resetStripeElements();
            setTimeout(initStripeElements, 50);
          });

          Livewire.on('funds-check-result', handleCheckResult);

          // Init immediately when funds modal opens (and "new" card is selected)
          Livewire.on('modal-opened', (event) => {
            const modalName = event[0]?.modal;
            if (modalName === 'funds') {
              setTimeout(initStripeElements, 150);
            }
          });
        });

        // Also init if modal already visible after morph
        setTimeout(initStripeElements, 500);
      })();
    </script>
  @endscript
@endif
