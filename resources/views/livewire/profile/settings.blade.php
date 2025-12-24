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

                <div class="w-full">
                  <div x-data="{ type: 'password' }" class="">
                    <x-form.input 
                      label="New Password"
                      name="security.password"
                      wire:model.defer="security.password"
                      x-bind:type="type"
                      tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols."
                    >
                      <x-slot name="icon">
                        <div x-on:click="() => type = (type == 'password') ? 'text' : 'password'" class="absolute top-1/2 right-9 translate-y-[-50%] hover:cursor-pointer">
                          <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
                        </div>
                      </x-slot>
                    </x-form.input>
                  </div>
                  <div x-data="passwordStrength('security.password')" class="mt-2">
                    <div x-show="password.length > 0" x-transition>
                      <div class="flex items-center gap-2 mb-1">
                        <div class="flex-1 h-1.5 bg-gray/20 rounded-full overflow-hidden">
                          <div x-bind:class="{
                            'bg-red-500': strength === 'weak',
                            'bg-yellow-500': strength === 'medium',
                            'bg-green-500': strength === 'strong'
                          }" x-bind:style="'width: ' + (strength === 'weak' ? '33%' : strength === 'medium' ? '66%' : '100%')" class="h-full transition-all duration-300"></div>
                        </div>
                        <span x-bind:class="{
                          'text-red-500': strength === 'weak',
                          'text-yellow-500': strength === 'medium',
                          'text-green-500': strength === 'strong'
                        }" x-text="strengthText" class="text-xs font-medium"></span>
                      </div>
                      <p x-show="strength === 'weak'" class="text-xs text-red-500 mt-1">This password is too weak. Please use a medium or strong password with uppercase and lowercase letters, numbers, and symbols.</p>
                    </div>
                  </div>
                </div>

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
                    x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'payment-method' })"
                  >
                    Add Payment Method
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
                        x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'payment-method' })"
                      >
                        + Add Payment Method
                      </x-btn>
                    </div>
                @endforelse
              </div>
            </x-profile.section>





            {{-- SELLER SETTINGS --}}
            @if($isSeller)
            <x-profile.section title="Seller Settings">
              <div class="flex flex-col justify-start items-start gap-2.5">
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
      function passwordStrength(wireModel) {
        return {
          password: '',
          strength: 'weak',
          strengthText: 'Weak',
          
          init() {
            const input = document.querySelector('input[name="security.password"]');
            if (input) {
              // Check initial value
              if (input.value) {
                this.checkStrength(input.value);
              }
              // Listen to input events
              input.addEventListener('input', (e) => {
                this.checkStrength(e.target.value);
              });
            }
          },
          
          checkStrength(value) {
            this.password = value || '';
            if (!this.password) {
              this.strength = 'weak';
              this.strengthText = 'Weak';
              return;
            }
            
            let score = 0;
            const length = this.password.length;
            
            // Length checks
            if (length >= 8) score++;
            if (length >= 12) score++;
            if (length >= 16) score++;
            
            // Character variety
            if (/[a-z]/.test(this.password)) score++; // lowercase
            if (/[A-Z]/.test(this.password)) score++; // uppercase
            if (/\d/.test(this.password)) score++; // numbers
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(this.password)) score++; // special chars
            
            // Additional checks
            if (length >= 8 && /[a-z]/.test(this.password) && /[A-Z]/.test(this.password) && /\d/.test(this.password)) {
              score++; // has lowercase, uppercase, and number
            }
            if (length >= 8 && /[a-z]/.test(this.password) && /[A-Z]/.test(this.password) && /\d/.test(this.password) && /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(this.password)) {
              score++; // has all character types
            }
            
            // Determine strength
            if (score <= 3) {
              this.strength = 'weak';
              this.strengthText = 'Weak';
            } else if (score <= 6) {
              this.strength = 'medium';
              this.strengthText = 'Medium';
            } else {
              this.strength = 'strong';
              this.strengthText = 'Strong';
            }
          }
        }
      }
    </script>
  @endscript
