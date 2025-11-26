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
      let clientSecret = @js($clientSecret);
      const componentId = @js($this->getId());

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

      // Function to ensure Stripe is loaded (must be defined early)
      const ensureStripeLoaded = () => {
        return new Promise((resolve) => {
          if (typeof window.Stripe !== 'undefined') {
            resolve();
            return;
          }

          // Check if script is loading
          const existingScript = document.querySelector('script[src*="js.stripe.com"]');
          
          if (existingScript) {
            // Wait for it to load
            const checkInterval = setInterval(() => {
              if (typeof window.Stripe !== 'undefined') {
                clearInterval(checkInterval);
                resolve();
              }
            }, 50);
            
            // Timeout after 5 seconds
            setTimeout(() => {
              clearInterval(checkInterval);
              if (typeof window.Stripe === 'undefined') {
                console.error('Stripe.js failed to load');
              }
              resolve();
            }, 5000);
          } else {
            // Load it dynamically
            const script = document.createElement('script');
            script.src = 'https://js.stripe.com/v3/';
            script.async = true;
            script.onload = () => resolve();
            script.onerror = () => {
              console.error('Failed to load Stripe.js');
              resolve(); // Resolve anyway to prevent hanging
            };
            document.head.appendChild(script);
          }
        });
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

        if (container.dataset.initialized === 'true') {
          return;
        }

        // Wait for Stripe.js to load with better retry logic
        if (typeof window.Stripe === 'undefined') {
          // Check if script is already in the DOM
          const existingScript = document.querySelector('script[src*="js.stripe.com"]');
          
          if (!existingScript) {
            // Load Stripe.js dynamically
            const script = document.createElement('script');
            script.src = 'https://js.stripe.com/v3/';
            script.async = true;
            script.onload = () => {
              console.log('Stripe.js loaded dynamically');
              setTimeout(init, 100);
            };
            script.onerror = () => {
              console.error('Failed to load Stripe.js');
              showError(errorBlock, 'Failed to load payment library. Please refresh the page.');
            };
            document.head.appendChild(script);
          } else if (existingScript.getAttribute('data-loading') !== 'true') {
            // Script exists but not loaded yet, wait for it
            existingScript.setAttribute('data-loading', 'true');
            const checkStripe = setInterval(() => {
              if (typeof window.Stripe !== 'undefined') {
                clearInterval(checkStripe);
                setTimeout(init, 100);
              }
            }, 100);
            
            // Timeout after 10 seconds
            setTimeout(() => {
              clearInterval(checkStripe);
              if (typeof window.Stripe === 'undefined') {
                showError(errorBlock, 'Payment library failed to load. Please refresh the page.');
              }
            }, 10000);
          }
          
          return;
        }
        
        if (typeof window.Stripe !== 'function') {
          setTimeout(init, 200);
          return;
        }

        if (!stripeKey || !clientSecret) {
          console.error('Stripe configuration missing:', { stripeKey: !!stripeKey, clientSecret: !!clientSecret });
          showError(errorBlock, 'Payment configuration error. Please refresh the page.');
          return;
        }

        try {
          initialized = true;
          container.dataset.initialized = 'true';
          submitButton = document.getElementById('pm-submit');
          window.pmFormElement = form;

          stripeInstance = Stripe(stripeKey);
          elementsInstance = stripeInstance.elements({ clientSecret });
          paymentElement = elementsInstance.create('payment');
          paymentElement.mount(container);
          
          console.log('Stripe Elements initialized successfully');
        } catch (error) {
          console.error('Failed to initialize Stripe Elements:', error);
          showError(errorBlock, 'Failed to initialize payment form. Please refresh the page.');
          initialized = false;
          delete container.dataset.initialized;
        }

        formListener = async (event) => {
          event.preventDefault();

          disableButton();
          clearError(errorBlock);

          const cardholderName = (cardholderInput?.value ?? '').trim();
          
          // Use confirmSetup with Payment Element (correct syntax)
          const setupOptions = {
            elements: elementsInstance,
            redirect: 'if_required',
          };
          
          // Add billing details if cardholder name is provided
          if (cardholderName) {
            setupOptions.confirmParams = {
              payment_method_data: {
                billing_details: {
                  name: cardholderName,
                },
              },
            };
          }
          
          const { error, setupIntent } = await stripeInstance.confirmSetup(setupOptions);

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

          // Call Livewire component method
          const component = Livewire.find(componentId);
          if (component) {
            component.call('attachPaymentMethod', setupIntent.payment_method);
          } else {
            console.error('Livewire component not found');
            showError(errorBlock, 'Failed to save payment method. Please try again.');
            enableButton();
          }
        };

        form.addEventListener('submit', formListener);
      };

      // Handle payment-method-open event
      const handlePaymentMethodOpen = () => {
        reset();
        ensureStripeLoaded().then(() => {
          setTimeout(init, 150);
        });
      };
      
      // Register listener immediately
      if (typeof Livewire !== 'undefined') {
        Livewire.on('payment-method-open', handlePaymentMethodOpen);
        Livewire.on('payment-method-open-js', handlePaymentMethodOpen);
      } else {
        // Wait for Livewire to load
        document.addEventListener('livewire:init', () => {
          Livewire.on('payment-method-open', handlePaymentMethodOpen);
          Livewire.on('payment-method-open-js', handlePaymentMethodOpen);
        });
      }
      
      // Also listen via window events
      window.addEventListener('payment-method-open', handlePaymentMethodOpen);

      Livewire.on('payment-method-close', reset);
      Livewire.on('payment-method-add-failed', enableButton);

      // Listen for client secret updates
      Livewire.on('payment-method-client-secret-updated', (event) => {
        if (event[0]?.clientSecret) {
          clientSecret = event[0].clientSecret;
        }
      });

      // Also listen to modal-opened event for payment-method
      Livewire.on('modal-opened', (event) => {
        if (event[0]?.modal === 'payment-method') {
          reset();
          
          // Trigger refresh of SetupIntent (component will dispatch client-secret-updated event)
          const component = Livewire.find(componentId);
          if (component) {
            component.call('refreshSetupIntent', { modal: 'payment-method' });
          }
          
          setTimeout(() => {
            ensureStripeLoaded().then(() => {
              setTimeout(init, 200);
            });
          }, 200);
        }
      });

      document.addEventListener('livewire:load', () => {
        Livewire.hook('morphed', () => {
          // Check if payment-method modal is visible
          const modal = document.querySelector('[wire\\:id*="modals.payment-method"]');
          if (modal && !initialized) {
            ensureStripeLoaded().then(() => {
              setTimeout(init, 100);
            });
          }
        });
      });

      window.addEventListener('modalClosing', reset);
      
      // Initialize on page load if modal is already open
      const checkAndInit = () => {
        const container = document.getElementById('pm-payment-element');
        if (container && container.offsetParent !== null && !initialized) {
          // Element is visible
          ensureStripeLoaded().then(() => {
            setTimeout(init, 100);
          });
        }
      };
      
      // Check after a delay to ensure DOM is ready
      setTimeout(checkAndInit, 500);
    })();
  </script>
@endscript
