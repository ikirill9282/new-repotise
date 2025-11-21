<div>
    @if($isSeller)
        <x-profile.creator-plus-banner class="mb-6" />
    @endif
    
    <div class="flex justify-start items-start flex-col-reverse lg:flex-row !gap-10">

        <div class="w-full">
            {{-- PROFILE --}}
            <x-profile.section title="Profile">
              <x-slot name="titleSlot">
                <div class="flex justify-start items-center gap-2">
                  <x-btn class="!py-1.5 !px-4 !w-auto" wire:click.prevent="saveAll">Save</x-btn>
                  <x-btn class="!py-1.5 !px-4 !w-auto" outlined wire:click.prevent="cancel">Cancel</x-btn>
                </div>
              </x-slot>

              <div class="flex flex-col justify-start items-start !gap-6">
                <x-form.input 
                  label="Full Name" 
                  wire:model.defer="profile.full_name"
                  tooltipText="Enter your full name as it appears on your payment method for billing purposes."
                />

                <x-form.input 
                  label="Username" 
                  wire:model.defer="profile.username"
                  tooltipText="Username is the user's public identifier. Can only be changed once."
                />
              </div>
            </x-profile.section>

            {{-- SECURITY --}}
            <x-profile.section title="Security" :tooltip="false">
              <div class="flex flex-col justify-start items-start !gap-6">
                <div 
                  x-data="{}" 
                  x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'change-email' })" 
                  class="w-full cursor-pointer"
                >
                  <x-form.input 
                    label="Email"
                    type="email" 
                    wire:model.defer="security.email"
                    tooltipText="Email is used for account security, product updates and news."
                    readonly
                    class="cursor-pointer"
                  />
                  <div class="text-xs text-gray mt-1 pl-1">Click to change your email address.</div>
                </div>

                <x-form.input 
                  label="New Password"
                  type="password"
                  name="security.password"
                  wire:model.defer="security.password"
                  tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols."
                />

                <x-form.input 
                  label="Confirm Password"
                  type="password"
                  name="security.password_confirmation"
                  wire:model.defer="security.password_confirmation"
                  tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols."
                />

                <x-form.toggle 
                  label="Two-Factor Authentication"
                  wire:model="security.twofa"
                  tooltipText="Enable 2FA for extra security using an authenticator app."
                  wire:change="handleTwofaToggle($event.target.checked)"
                />
              </div>
            </x-profile.section>

            {{-- PAYMENT --}}
            <x-profile.section title="Payment/Payout Methods" :tooltip="false">
              <x-slot name="titleSlot">
                @push('head')
                  <script src="https://js.stripe.com/v3/"></script>
                @endpush

                <div class="flex items-center gap-2 sm:ml-auto">
                  <x-btn class="!text-sm !px-4 !py-1.5 !w-auto transition !bg-second !border-second hover:!bg-active hover:!border-active disabled:opacity-60 disabled:cursor-not-allowed"
                    wire:click="startAddPaymentMethod"
                    wire:loading.attr="disabled"
                    wire:target="startAddPaymentMethod"
                    :disabled="$showPaymentForm"
                  >
                    {{ $showPaymentForm ? 'Card form active' : 'Add Payment Method' }}
                  </x-btn>
                </div>
              </x-slot>
              
              <div class="flex flex-col justify-start items-start gap-2">
                @forelse($paymentMethods as $method)
                  <div class="flex flex-col w-full">
                    <x-form.payment-method 
                      wire:model="selectedPaymentMethod" 
                      name="payment_method" 
                      :value="$method['id']"
                      :tooltip="false"
                      :editor="false"
                      :icons="false"
                      :brand="$method['label']"
                      :last4="$method['last4']"
                      class="bg-transparent group-has-[input]:!px-0"
                    />
                    <div class="text-xs text-gray ml-7 mt-1">
                      Expires {{ $method['expires'] }}
                    </div>
                  </div>
                @empty
                    <div class="bg-light rounded !p-4 text-gray flex flex-col gap-2 w-full">
                      <span>No payment methods saved yet.</span>
                      <x-btn second class="!inline-block !text-sm !px-3 !py-1 w-auto disabled:opacity-60 disabled:cursor-not-allowed"
                        wire:click="startAddPaymentMethod"
                        wire:loading.attr="disabled"
                        wire:target="startAddPaymentMethod"
                        :disabled="$showPaymentForm"
                      >
                        + Add Payment Method
                      </x-btn>
                    </div>
                @endforelse
              </div>

              @if($showPaymentForm)
                <div class="w-full mt-5" wire:key="settings-payment-form-wrapper">
                  <div class="bg-light rounded-xl border border-gray/20 p-4 sm:p-6 shadow-sm" wire:ignore>
                    <div class="text-base font-semibold mb-3">Add a new card</div>
                    <form id="settings-payment-form" class="flex flex-col gap-4">
                      <x-form.input
                        id="settings-cardholder-name"
                        label="Cardholder Name"
                        placeholder="Name on card"
                        :tooltip="false"
                      />

                      <div class="flex flex-col gap-2">
                        <label class="text-sm text-gray">Card Details</label>
                        <div id="settings-payment-element" class="w-full px-3 py-3 rounded-lg border border-gray/40 bg-white shadow-xs"></div>
                        <p id="settings-payment-errors" class="text-sm text-red-500 hidden"></p>
                      </div>

                      <div class="flex justify-center items-center gap-3 pt-1">
                        <x-btn class="!text-sm sm:!text-base !w-auto sm:!px-10" type="button" outlined
                          wire:click="cancelPaymentMethodForm"
                        >
                          Cancel
                        </x-btn>
                        <x-btn class="!text-sm sm:!text-base !grow" id="settings-payment-submit" type="submit">
                          Save Card
                        </x-btn>
                      </div>
                    </form>
                  </div>
                </div>
              @endif
            </x-profile.section>





            {{-- SELLER SETTINGS --}}
            @if($isSeller)
            <x-profile.section title="Seller Settings">
              <div class="flex flex-col justify-start items-start gap-2.5">
                <x-form.select 
                  name="return_policy"
                  wire:model="selectedReturnPolicy"
                  label="Return Policy" 
                  labelClass="{{ $selectedReturnPolicy ? '' : 'text-gray' }}"
                  tooltipText="Specify the return policy that will apply to all your products. Buyers will see this information on your product pages."
                  :options="collect($returnPolicies)->pluck('title', 'id')->toArray()"
                />

                <x-form.toggle label="Creator Page Visibility" wire:model="preferences.creator_visible" tooltipText="Control whether your Creator Page is publicly visible on the marketplace." />
                
                <x-form.toggle label="Show 'Donate' Button" wire:model="preferences.show_donate" tooltipText="Show or hide the 'Donate' button on your Creator Page." />
                
                <x-form.toggle label="Show 'Products' Section" wire:model="preferences.show_products" tooltipText="Control whether the 'Products' section is displayed on your Creator Page." />
                
                <x-form.toggle label="Show 'Travel Insights'" wire:model="preferences.show_insights" tooltipText="Control whether the 'Travel Insights' section is displayed on your Creator Page." />

              </div>
            </x-profile.section>
            @endif


            {{-- EMAIL NOTIFICATIONS --}}
            <x-profile.section title="Email Notifications">
              @php
                $notificationTooltips = $isSeller
                  ? [
                      'product_updates' => 'Get emails about product updates and new releases.',
                      'referral_updates' => 'Updates on your referral program.',
                      'news_updates' => 'TrekGuider News & Updates.',
                      'insights_updates' => 'Get notifications about new articles by creators you follow.',
                    ]
                  : [
                      'product_updates' => 'Receive emails about updates to your purchased products and new product releases.',
                      'referral_updates' => 'Updates on your referral program.',
                      'news_updates' => 'Platform news and updates, new features.',
                      'insights_updates' => 'Notifications about new articles by authors you have subscribed to.',
                    ];
              @endphp
              <div class="grid grid-cols-1 sm:grid-cols-2 items-stretch gap-2.5">
                @foreach($notificationLabels as $key => $label)
                  <x-form.toggle 
                    wrapClass="h-full" 
                    labelClass="gap-2 md:gap-0" 
                    :label="$label"
                    wire:model="notifications.{{ $key }}"
                    :tooltipText="$notificationTooltips[$key] ?? 'Manage your notification preferences.'"
                  />
                @endforeach
              </div>
            </x-profile.section>


            {{-- DELETE --}}
            <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'delete-account' })">Delete Account</x-link>
        </div>

        <div 
            x-data="{ 
                uploadAvatar() {
                    const input = document.getElementById('avatar-upload');
                    if (input) {
                        input.click();
                    }
                }
            }"
            class="!w-25 !h-25 md:!w-45 md:!h-45 shrink-0 rounded-full overflow-hidden mr-10 relative group cursor-pointer"
            x-on:click="uploadAvatar()"
            title="Click to upload avatar"
        >
            @php
                $avatarSrc = $user->avatar;
                if ($avatar && is_object($avatar)) {
                    try {
                        $avatarSrc = $avatar->temporaryUrl();
                    } catch (\Exception $e) {
                        // Если временный URL недоступен, используем текущую аватарку
                    }
                }
            @endphp
            <img 
                class="object-cover w-full h-full transition-opacity group-hover:opacity-80" 
                src="{{ $avatarSrc }}" 
                alt="Avatar"
            >
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <span class="text-white text-xs font-medium">Change</span>
            </div>
            <x-tooltip class="!absolute -top-3 right-0" message="Visible to other users. Upload JPG, PNG, or JPEG (max 5MB)."></x-tooltip>
            <input 
                type="file" 
                id="avatar-upload" 
                wire:model="avatar"
                accept="image/jpeg,image/jpg,image/png,image/gif"
                class="hidden"
            >
            <div wire:loading wire:target="avatar" class="absolute inset-0 bg-black/50 flex items-center justify-center">
                <div class="text-white text-sm">Uploading...</div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
      (() => {
        let stripeInstance = null;
        let elementsInstance = null;
        let paymentElement = null;
        let formEl = null;
        let submitBtn = null;
        let submitHandler = null;
        let cardholderInput = null;
        let errorBlock = null;
        let currentSecret = null;
        let publishableKey = null;
        const settingsComponentId = @js($this->getId());

        const callComponentMethod = (method, ...args) => {
          if (!settingsComponentId || typeof Livewire?.find !== 'function') {
            return false;
          }

          const component = Livewire.find(settingsComponentId);

          if (!component || typeof component.call !== 'function') {
            return false;
          }

          component.call(method, ...args);
          return true;
        };

        const disableButton = () => {
          if (submitBtn) {
            submitBtn.setAttribute('disabled', 'disabled');
            submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
          }
        };

        const enableButton = () => {
          if (submitBtn) {
            submitBtn.removeAttribute('disabled');
            submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
          }
        };

        const clearError = () => {
          if (errorBlock) {
            errorBlock.textContent = '';
            errorBlock.classList.add('hidden');
          }
        };

        const showError = (message) => {
          if (errorBlock) {
            errorBlock.textContent = message || 'Something went wrong.';
            errorBlock.classList.remove('hidden');
          }
        };

        const reset = () => {
          if (formEl && submitHandler) {
            formEl.removeEventListener('submit', submitHandler);
          }

          if (paymentElement) {
            paymentElement.unmount();
          }

          stripeInstance = null;
          elementsInstance = null;
          paymentElement = null;
          formEl = null;
          submitBtn = null;
          submitHandler = null;
          cardholderInput = null;
          errorBlock = document.getElementById('settings-payment-errors') || null;
          clearError();
          enableButton();
        };

        const handleSubmit = async (event) => {
          event.preventDefault();

          if (!stripeInstance || !elementsInstance || !currentSecret) {
            showError('Stripe is not ready yet. Please try again.');
            return;
          }

          disableButton();
          clearError();

          const { error, setupIntent } = await stripeInstance.confirmSetup({
            elements: elementsInstance,
            redirect: 'if_required',
          });

          if (error) {
            showError(error.message);
            enableButton();
            return;
          }

          if (!setupIntent?.payment_method) {
            showError('Unable to add payment method. Please try again.');
            enableButton();
            return;
          }

          if (!callComponentMethod('completeAddPaymentMethod', setupIntent.payment_method)) {
            Livewire.dispatch('settings-payment-method-submitted', {
              paymentMethodId: setupIntent.payment_method,
            });
          }
        };

        const mountElement = () => {
          const form = document.getElementById('settings-payment-form');
          const container = document.getElementById('settings-payment-element');

          if (!form || !container) {
            setTimeout(mountElement, 40);
            return;
          }

          if (typeof window.Stripe !== 'function' || !publishableKey || !currentSecret) {
            setTimeout(mountElement, 40);
            return;
          }

          formEl = form;
          submitBtn = document.getElementById('settings-payment-submit');
          cardholderInput = document.getElementById('settings-cardholder-name');
          errorBlock = document.getElementById('settings-payment-errors');

          stripeInstance = Stripe(publishableKey);
          elementsInstance = stripeInstance.elements({ 
            clientSecret: currentSecret,
            appearance: {
              theme: 'stripe',
            },
          });
          paymentElement = elementsInstance.create('payment');
          paymentElement.mount(container);

          submitHandler = handleSubmit;
          formEl.addEventListener('submit', submitHandler);
        };

        Livewire.on('settings-payment-form-ready', (payload) => {
          const data = Array.isArray(payload) ? payload[0] : payload;
          publishableKey = data?.publishableKey ?? null;
          currentSecret = data?.clientSecret ?? null;
          reset();
          setTimeout(mountElement, 50);
        });

        Livewire.on('settings-payment-form-reset', () => {
          reset();
          currentSecret = null;
          publishableKey = null;
        });

        Livewire.on('settings-payment-form-error', () => {
          enableButton();
        });

        document.addEventListener('livewire:navigating', () => {
          reset();
        });
      })();
    </script>
  @endscript
