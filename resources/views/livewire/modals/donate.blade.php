@once
  @push('head')
    <script src="https://js.stripe.com/v3/"></script>
  @endpush
@endonce

<div>
  @if($unavailable)
    <div class="text-center py-8">
      <div class="text-2xl font-semibold mb-4">Donations Unavailable</div>
      <p class="text-gray mb-6">Donations are currently unavailable for this creator.</p>
      <x-btn wire:click.prevent="$dispatch('closeModal')" outlined>Close</x-btn>
    </div>
  @else
    {{-- HEADER --}}
    <div class="text-2xl font-semibold mb-2">Donate to {{ $seller?->getName() ?? 'Creator' }}</div>
    <div class="text-sm text-gray pb-6 mb-4 border-b-1 border-gray/30">
      Show your appreciation by sending a donation. Your support helps creators continue making great content.
    </div>

    {{-- FORM --}}
    <form class="flex flex-col gap-4 pb-6 mb-4 border-b-1 border-gray/30">
      {{-- GUEST FIELDS (if not authenticated) --}}
      @if(!$donor)
        <x-form.input 
          type="text"
          label="Full Name"
          placeholder="Your name"
          :tooltip="false"
          wire:model.live="guest.fullname"
        />
        <x-form.input 
          type="email"
          label="Email"
          placeholder="your@email.com"
          :tooltip="false"
          wire:model.live="guest.email"
        />
      @endif

      {{-- AMOUNT --}}
      <x-form.input 
        type="number"
        step="0.01"
        min="1"
        max="10000"
        label="Donation Amount"
        placeholder="$10"
        :tooltip="false"
        inputClass="!text-base"
        wire:model.live="amount"
      />

      {{-- OPTIONS --}}
      <x-form.checkbox 
        wire:model.live="coverFees"
        :tooltip="true"
        tooltipText="Help {{ $seller?->getName() ?? 'the creator' }} receive the full donation amount by covering payment processing fees."
        label="Cover fees (+{{ rtrim(rtrim(number_format($processingPercent, 2, '.', ''), '0'), '.') }}% + ${{ number_format($processingFlat, 2) }})"
      />

      <x-form.checkbox 
        wire:model.live="anonymous"
        :tooltip="true"
        tooltipText="Your donation will be anonymous. Your name won't be publicly listed."
        label="Donate anonymously"
      />

      <x-form.checkbox 
        wire:model.live="monthlySupport"
        :tooltip="true"
        tooltipText="Support {{ $seller?->getName() ?? 'the creator' }} every month. Manage or cancel anytime in your account settings."
        label="Monthly support"
      />

      {{-- MESSAGE --}}
      <div>
        <label class="block text-sm text-gray mb-1">Message (Optional)</label>
        <textarea 
          wire:model.live="message"
          class="w-full px-3 py-2 rounded-lg border border-gray/40 bg-white resize-none"
          rows="3"
          placeholder="Add a message (optional)"
          maxlength="500"
        ></textarea>
        <div class="text-xs text-gray mt-1">
          {{ strlen($message) }}/500 characters
        </div>
      </div>

      {{-- PAYMENT METHODS TITLE --}}
      <div class="text-2xl font-semibold mt-2">
        Choose Payment Method
      </div>

      {{-- PAYMENT METHODS --}}
      @if(!empty($paymentMethods))
        <div class="flex flex-col justify-start items-start gap-2.5 group">
          @foreach($paymentMethods as $method)
            <x-form.payment-method
              wire:model="selectedPaymentMethod"
              name="donate_payment_method" 
              :value="$method['id']"
              :tooltip="false"
              :editor="false"
              :icons="false"
              :brand="$method['label']"
              :last4="$method['last4']"
              class="bg-transparent group-has-[input]:!px-0"
            />
          @endforeach
          <x-btn 
            second 
            class="!inline-block !text-sm !px-3 !py-1.5 w-auto mt-1"
            x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'payment-method' })"
          >
            + Add Payment Method
          </x-btn>
        </div>
      @else
        <div class="bg-light rounded !p-4 text-gray flex flex-col gap-2 mb-4">
          <span>No payment methods saved yet.</span>
          <x-form.payment-method
            wire:model="selectedPaymentMethod"
            name="donate_payment_method" 
            value="new"
            :tooltip="false"
            :editor="false"
            :icons="false"
            brand="New Card"
            last4=""
            class="bg-transparent group-has-[input]:!px-0"
          />
          <x-btn 
            second 
            class="!inline-block !text-sm !px-3 !py-1 w-auto mt-2"
            x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'payment-method' })"
          >
            + Add Payment Method
          </x-btn>
        </div>
      @endif

      {{-- NEW PAYMENT METHOD (Stripe Elements) --}}
      @if($selectedPaymentMethod === 'new' && $setupIntentSecret)
        <div wire:ignore class="mt-4">
          <div id="donate-payment-element" class="w-full px-3 py-3 rounded-lg border border-gray/40 bg-white shadow-xs"></div>
          <p id="donate-errors" class="text-sm text-red-500 hidden mt-2"></p>
        </div>
      @endif
    </form>

    {{-- SUMMARY --}}
    <div class="w-full flex flex-col items-stretch gap-2 mb-4">
      <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
        <div>Donation Amount:</div>
        <div class="text-dark font-semibold">{{ currency($summary['donation'] ?? $amount) }}</div>
      </div>
      @if($summary['platform_fee'] ?? 0 > 0)
        <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
          <div>Platform Fee ({{ $platformPercent }}%):</div>
          <div class="text-dark font-semibold">{{ currency($summary['platform_fee'] ?? 0) }}</div>
        </div>
      @endif
      @if($summary['stripe_fee'] ?? 0 > 0)
        <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
          <div>Processing Fee:</div>
          <div class="text-dark font-semibold">{{ currency($summary['stripe_fee'] ?? 0) }}</div>
        </div>
      @endif
      <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray font-semibold">
        <div>Total Charge:</div>
        <div class="text-dark">{{ currency($summary['total_charge'] ?? $amount) }}</div>
      </div>
      <div class="flex justify-between items-stretch pb-0.5 border-b-1 border-gray/50 border-dashed text-gray">
        <div>Creator Receives:</div>
        <div class="text-dark font-semibold">{{ currency($summary['seller_receive'] ?? 0) }}</div>
      </div>
      <div class="text-xs text-gray mt-2">
        @if($coverFees)
          Processing fees will be added to your total charge, so {{ currency($summary['donation'] ?? $amount) }} goes to the creator.
        @else
          Processing fees will be deducted from your donation, so the creator receives {{ currency($summary['seller_receive'] ?? 0) }}.
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
        wire:click="checkDonation"
        wire:loading.attr="disabled"
        id="donate-submit-btn"
        type="button"
      >
        <span id="donate-btn-text" wire:loading.remove wire:target="checkDonation">
          @if($monthlySupport)
            Start Monthly Support
          @else
            Send Donation
          @endif
        </span>
        <span id="donate-btn-processing" wire:loading wire:target="checkDonation" style="display: none;">Processing...</span>
      </x-btn>
    </div>
  @endif
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
        let isProcessing = false;

        // Log button click
        document.addEventListener('DOMContentLoaded', () => {
          const submitBtn = document.getElementById('donate-submit-btn');
          if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
              console.log('Donation: Send Donation button clicked', {
                componentId: componentId,
                timestamp: new Date().toISOString(),
              });
            });
          }
        });

        if (!stripeKey) {
          console.error('Stripe publishable key is missing');
          return;
        }

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

        const clearError = () => {
          const errorEl = document.getElementById('donate-errors');
          if (errorEl) {
            errorEl.textContent = '';
            errorEl.classList.add('hidden');
          }
        };

        const showError = (message) => {
          const errorEl = document.getElementById('donate-errors');
          if (errorEl) {
            errorEl.textContent = message || 'Something went wrong.';
            errorEl.classList.remove('hidden');
          }
        };

        const initStripeElements = () => {
          const container = document.getElementById('donate-payment-element');
          if (!container) {
            return;
          }

          if (container.dataset.initialized === 'true') {
            return;
          }

          if (!setupIntentSecret) {
            return;
          }

          ensureStripeLoaded().then(() => {
            if (typeof window.Stripe === 'undefined') {
              showError('Payment library failed to load. Please refresh the page.');
              return;
            }

            try {
              stripeInstance = Stripe(stripeKey);
              elementsInstance = stripeInstance.elements({
                clientSecret: setupIntentSecret,
                appearance: {
                  theme: 'stripe',
                },
              });
              paymentElement = elementsInstance.create('payment');
              paymentElement.mount(container);
              container.dataset.initialized = 'true';
              initialized = true;
            } catch (error) {
              console.error('Failed to initialize Stripe Elements:', error);
              showError('Failed to initialize payment form. Please refresh the page.');
            }
          });
        };

        const resetStripeElements = () => {
          if (paymentElement) {
            try {
              paymentElement.unmount();
            } catch (e) {
              console.error('Error unmounting payment element:', e);
            }
            paymentElement = null;
          }
          elementsInstance = null;
          initialized = false;
          const container = document.getElementById('donate-payment-element');
          if (container) {
            delete container.dataset.initialized;
          }
        };

        const setButtonProcessing = (processing) => {
          const btn = document.getElementById('donate-submit-btn');
          const text = document.getElementById('donate-btn-text');
          const processingText = document.getElementById('donate-btn-processing');
          if (btn && text && processingText) {
            if (processing) {
              btn.setAttribute('disabled', 'disabled');
              text.style.display = 'none';
              processingText.style.display = 'inline';
            } else {
              btn.removeAttribute('disabled');
              text.style.display = 'inline';
              processingText.style.display = 'none';
            }
          }
          isProcessing = processing;
        };

        // Listen for checkDonation result
        document.addEventListener('livewire:init', () => {
          Livewire.on('donation-check-result', async (event) => {
            const result = Array.isArray(event) ? (event[0] || event) : event;
            
            console.log('Donation: donation-check-result event received', result);
            
            if (result.error) {
              console.error('Donation: Error in check result', result);
              setButtonProcessing(false);
              return;
            }

            // Keep button in processing state - it will be reset by donation-processing-ended
            // or when modal closes
            setButtonProcessing(true);
            console.log('Donation: Processing donation with action', result.action);

            if (result.action === 'create') {
              // New payment method - confirm setup intent
              if (!paymentElement || !setupIntentSecret) {
                showError('Payment form is not ready. Please try again.');
                setButtonProcessing(false);
                return;
              }

              clearError();

              try {
                const { error, setupIntent } = await stripeInstance.confirmSetup({
                  elements: elementsInstance,
                  redirect: 'if_required',
                });

                if (error) {
                  const component = Livewire.find(componentId);
                  if (component) {
                    const errorMessage = error.message || 'Unable to add payment method.';
                    const errorCode = error.code || 'setup_failed';
                    component.call('showDonationError', errorMessage, errorCode);
                  } else {
                    showError(error.message);
                    setButtonProcessing(false);
                  }
                  return;
                }

                if (!setupIntent?.payment_method) {
                  const component = Livewire.find(componentId);
                  if (component) {
                    component.call('showDonationError', 'Unable to add payment method. Please try again.', 'setup_failed');
                  } else {
                    showError('Unable to add payment method. Please try again.');
                    setButtonProcessing(false);
                  }
                  return;
                }

                // Call makeDonation with the new payment method
                console.log('Donation: Calling makeDonation with new payment method', setupIntent.payment_method);
                const component = Livewire.find(componentId);
                if (component && component.$wire) {
                  // Use $wire.dispatch to send event to this component
                  console.log('Donation: Using component.$wire.dispatch');
                  component.$wire.dispatch('makeDonation', {
                    pm_id: setupIntent.payment_method,
                  });
                } else {
                  console.warn('Donation: Component not found or $wire not available, using global dispatch');
                  Livewire.dispatch('makeDonation', {
                    componentId: componentId,
                    pm_id: setupIntent.payment_method,
                  });
                }
              } catch (error) {
                console.error('Error confirming setup:', error);
                showError('An error occurred. Please try again.');
                setButtonProcessing(false);
              }
            // Note: For existing payment methods, handleMakeDonation is called directly from PHP
            // This branch should not be reached for existing payment methods anymore
            else if (result.action && result.action !== 'create') {
              console.warn('Donation: Unexpected action in donation-check-result', result.action);
              // This should not happen, but keep as fallback
              const component = Livewire.find(componentId);
              if (component && component.$wire) {
                component.$wire.dispatch('makeDonation', {
                  pm_id: result.action,
                });
              } else {
                Livewire.dispatch('makeDonation', {
                  componentId: componentId,
                  pm_id: result.action,
                });
              }
            }
          });
        });

        // Handle 3D Secure authentication
        document.addEventListener('livewire:init', () => {
          Livewire.on('donation-requires-action', async (event) => {
            const data = Array.isArray(event) ? (event[0] || event) : event;
            const clientSecret = data.clientSecret;
            
            // Keep button in processing state
            setButtonProcessing(true);
            
            if (!clientSecret || !stripeInstance) {
              showError('Payment authorization is required but could not be processed.');
              setButtonProcessing(false);
              return;
            }

            try {
              const { error, paymentIntent } = await stripeInstance.confirmCardPayment(clientSecret);
              
              if (error) {
                const component = Livewire.find(componentId);
                if (component) {
                  // Pass error message to component - it will dispatch donation-processing-ended
                  const errorMessage = error.message || 'Payment authorization failed.';
                  const errorCode = error.code || error.decline_code || 'authorization_failed';
                  component.call('showDonationError', errorMessage, errorCode);
                } else {
                  showError(error.message);
                  setButtonProcessing(false);
                }
                return;
              }

              // Payment confirmed, call donationResult
              // donationResult will call finalizeDonation which dispatches donation-processing-ended
              if (paymentIntent) {
                const component = Livewire.find(componentId);
                if (component) {
                  component.call('donationResult', 'success', paymentIntent.id);
                }
              }
            } catch (error) {
              console.error('Error confirming payment:', error);
              const component = Livewire.find(componentId);
              if (component) {
                // Component will dispatch donation-processing-ended
                const errorMessage = error.message || 'An error occurred during payment authorization.';
                const errorCode = error.code || 'authorization_error';
                component.call('showDonationError', errorMessage, errorCode);
              } else {
                showError('An error occurred during payment authorization.');
                setButtonProcessing(false);
              }
            }
          });
        });

        // Watch for selectedPaymentMethod changes
        document.addEventListener('livewire:init', () => {
          // Watch for property updates
          Livewire.hook('morph', ({ el, component }) => {
            if (component.name === 'modals.donate') {
              const componentInstance = Livewire.find(componentId);
              if (componentInstance) {
                const selectedMethod = componentInstance.get('selectedPaymentMethod');
                const newSetupSecret = componentInstance.get('setupIntentSecret');
                
                if (selectedMethod === 'new' && newSetupSecret) {
                  if (newSetupSecret !== setupIntentSecret) {
                    setupIntentSecret = newSetupSecret;
                    resetStripeElements();
                    setTimeout(initStripeElements, 100);
                  } else if (!initialized) {
                    setTimeout(initStripeElements, 100);
                  }
                } else if (selectedMethod !== 'new') {
                  resetStripeElements();
                }
              }
            }
          });

          Livewire.on('refreshPaymentMethods', () => {
            const component = Livewire.find(componentId);
            if (component) {
              const selectedMethod = component.get('selectedPaymentMethod');
              if (selectedMethod === 'new') {
                resetStripeElements();
                setupIntentSecret = component.get('setupIntentSecret');
                setTimeout(initStripeElements, 100);
              } else {
                resetStripeElements();
              }
            }
          });
        });


        // Listen for setupIntentSecret updates
        document.addEventListener('livewire:init', () => {
          Livewire.on('setup-intent-updated', (event) => {
            const secret = Array.isArray(event) ? (event[0] || event) : event;
            setupIntentSecret = secret;
            const component = Livewire.find(componentId);
            if (component) {
              const selectedMethod = component.get('selectedPaymentMethod');
              if (selectedMethod === 'new') {
                resetStripeElements();
                setTimeout(initStripeElements, 100);
              }
            }
          });

          // Cleanup on modal close
          Livewire.on('closeModal', () => {
            resetStripeElements();
            setButtonProcessing(false);
          });

          // Handle donation processing ended
          Livewire.on('donation-processing-ended', () => {
            setButtonProcessing(false);
          });
        });

        // Initialize if modal is already open
        document.addEventListener('livewire:init', () => {
          setTimeout(() => {
            const component = Livewire.find(componentId);
            if (component) {
              const selectedMethod = component.get('selectedPaymentMethod');
              setupIntentSecret = component.get('setupIntentSecret');
              if (selectedMethod === 'new' && setupIntentSecret) {
                initStripeElements();
              }
            }
          }, 500);
        });
      })();
    </script>
  @endscript
@endif
