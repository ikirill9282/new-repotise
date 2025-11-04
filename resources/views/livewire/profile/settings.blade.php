<div>
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
                  :tooltip="false"
                />

                <x-form.input 
                  label="Username" 
                  wire:model.defer="profile.username"
                  :tooltip="false"
                />
              </div>
            </x-profile.section>

            {{-- SECURITY --}}
            <x-profile.section title="Security">
              <div class="flex flex-col justify-start items-start !gap-6">
                <x-form.input 
                  label="Email"
                  type="email" 
                  wire:model.defer="security.email"
                  :tooltip="false"
                />

                <x-form.input 
                  label="New Password"
                  type="password"
                  wire:model.defer="security.password"
                  :tooltip="false"
                />

                <x-form.input 
                  label="Confirm Password"
                  type="password"
                  wire:model.defer="security.password_confirmation"
                  :tooltip="false"
                />

                <x-form.toggle 
                  label="Two-Factor Authentication"
                  wire:model="security.twofa"
                />
              </div>
            </x-profile.section>

            {{-- PAYMENT --}}
            <x-profile.section title="Payment Methods">
              <x-slot name="titleSlot">
                <x-btn class="!text-sm !px-4 !py-1.5 !w-auto sm:ml-auto transition !bg-second !border-second hover:!bg-active hover:!border-active"
                  x-data="{}"
                  x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'payout-method' })"
                >
                  Add Payment Method
                </x-btn>
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
                    <x-btn second class="!inline-block !text-sm !px-3 !py-1 w-auto"
                      x-data="{}"
                      x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'payout-method' })"
                    >
                      + Add Payment Method
                    </x-btn>
                  </div>
                @endforelse
              </div>
            </x-profile.section>


            {{-- PAYOUT --}}
            <x-profile.section title="Payout Methods">
              <x-slot name="titleSlot">
                <x-btn class="!text-sm !px-4 !py-1.5 !w-auto sm:ml-auto transition !bg-second !border-second hover:!bg-active hover:!border-active"
                  x-data="{}"
                  x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'payout-method' })"
                >
                  Add Payout Method
                </x-btn>
              </x-slot>
              
              <div class="flex flex-col justify-start items-start gap-2.5">
                @forelse($payoutMethods as $method)
                  <div class="flex flex-col w-full">
                    <x-form.payment-method 
                      wire:model="selectedPayoutMethod" 
                      name="payout_method" 
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
                    <span>No payout methods saved yet.</span>
                    <x-btn second class="!inline-block !text-sm !px-3 !py-1 w-auto"
                      x-data="{}"
                      x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'payout-method' })"
                    >
                      + Add Payout Method
                    </x-btn>
                  </div>
                @endforelse
              </div>
            </x-profile.section>


            {{-- SELLER SETTINGS --}}
            <x-profile.section title="Seller Settings">
              <div class="flex flex-col justify-start items-start gap-2.5">
                <x-form.select 
                  name="return_policy"
                  wire:model="selectedReturnPolicy"
                  label="Return Policy" 
                  labelClass="{{ $selectedReturnPolicy ? '' : 'text-gray' }}"
                  :tooltip="false"
                  :options="collect($returnPolicies)->pluck('title', 'id')->toArray()"
                />

                <x-form.toggle label="Creator Page Visibility" wire:model="preferences.creator_visible" />
                
                <x-form.toggle label="Show 'Donate' Button" wire:model="preferences.show_donate" />
                
                <x-form.toggle label="Show 'Products' Section" wire:model="preferences.show_products" />
                
                <x-form.toggle label="Show 'Travel Insights'" wire:model="preferences.show_insights" />

              </div>
            </x-profile.section>


            {{-- EMAIL NOTIFICATIONS --}}
            <x-profile.section title="Email Notifications">
              <div class="grid grid-cols-1 sm:grid-cols-2 items-stretch gap-2.5">
                @foreach($notificationLabels as $key => $label)
                  <x-form.toggle 
                    wrapClass="h-full" 
                    labelClass="gap-2 md:gap-0" 
                    :label="$label"
                    wire:model="notifications.{{ $key }}"
                  />
                @endforeach
              </div>
            </x-profile.section>


            {{-- DELETE --}}
            <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'delete-account' })">Delete Account</x-link>
        </div>

        <div class="!w-25 !h-25 md:!w-45 md:!h-45 shrink-0 rounded-full overflow-hidden mr-10">
            <img class="object-cover w-full h-full" src="{{ $user->avatar }}" alt="Avatar">
        </div>
    </div>
</div>
