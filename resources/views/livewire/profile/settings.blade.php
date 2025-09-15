<div>
    <div class="flex justify-start items-start flex-col-reverse lg:flex-row !gap-10">

        <div class="w-full">
            {{-- PROFILE --}}
            <x-profile.section title="Profile">
              <x-slot name="titleSlot">
                <div class="flex justify-start items-center gap-2">
                  <x-btn class="!py-1.5">Save</x-btn>
                  <x-btn class="!py-1.5" outlined>Cancel</x-btn>
                </div>
              </x-slot>

              <div class="flex flex-col justify-start items-start !gap-6">
                <x-form.input 
                  label="Full Name" 
                  value="{{ $user->options->full_name ?? $user->getName() }}" 
                  class=""
                />

                <x-form.input 
                  label="Username" 
                  value="{{ $user->options->full_name ?? $user->getName() }}" 
                  class=""
                />
              </div>
            </x-profile.section>

            {{-- SECURITY --}}
            <x-profile.section title="Security">
              <div class="flex flex-col justify-start items-start !gap-6">
                <x-form.input 
                  label="Email"
                  type="email" 
                  value="{{ $user->email }}" 
                  class=""
                />

                <x-form.input 
                  label="Password"
                  type="password"
                  value="{{ $user->options->full_name ?? $user->getName() }}" 
                  class=""
                />

                <x-form.toggle 
                  label="Two-Factor Authentication"
                />
              </div>
            </x-profile.section>

            {{-- PAYMENT --}}
            <x-profile.section title="Payment Methods">
              <x-slot name="titleSlot">
                <x-btn class="!text-sm !px-4 !py-1.5 !w-auto sm:ml-auto transition !bg-second !border-second hover:!bg-active hover:!border-active">
                  Add Payment Method
                </x-btn>
              </x-slot>
              
              <div class="flex flex-col justify-start items-start gap-2">
                <x-form.payment-method 
                  wire:model="form.payment_method" 
                  name="payment_method" 
                  value="payment_method_1"
                />

                <x-form.payment-method 
                  wire:model="form.payment_method" 
                  name="payment_method" 
                  value="payment_method_2"
                />
              </div>
            </x-profile.section>


            {{-- PAYOUT --}}
            <x-profile.section title="Payout Methods">
              <x-slot name="titleSlot">
                <x-btn class="!text-sm !px-4 !py-1.5 !w-auto sm:ml-auto transition !bg-second !border-second hover:!bg-active hover:!border-active">
                  Add Payout Method
                </x-btn>
              </x-slot>
              
              <div class="flex flex-col justify-start items-start gap-2.5">
                <x-form.payment-method 
                  wire:model="form.payout_method" 
                  name="payout_method" 
                  value="payout_method_1"
                />

                <x-form.payment-method 
                  wire:model="form.payout_method" 
                  name="payout_method" 
                  value="payout_method_1"
                />
              </div>
            </x-profile.section>


            {{-- SELLER SETTINGS --}}
            <x-profile.section title="Seller Settings">
              <div class="flex flex-col justify-start items-start gap-2.5">
                <x-form.select 
                  wire:model="form.return_policy"
                  label="Return Policy" 
                  labelClass="{{ $this->form['return_policy'] ? '' : 'text-gray' }}"
                />

                <x-form.toggle label="Creator Page Visibility" />
                
                <x-form.toggle label="Show 'Donate' Button" />
                
                <x-form.toggle label="Show 'Products' Section" />
                
                <x-form.toggle label="Show 'Travel Insights'" />

              </div>
            </x-profile.section>


            {{-- EMAIL NOTIFICATIONS --}}
            <x-profile.section title="Email Notifications">
              <div class="grid grid-cols-1 sm:grid-cols-2 items-stretch gap-2.5">
                <x-form.toggle wrapClass="h-full" labelClass="gap-2 md:gap-0" label="New Product Updates" />
                
                <x-form.toggle wrapClass="h-full" labelClass="gap-2 md:gap-0" label="Referral Program Updates" />
                
                <x-form.toggle wrapClass="h-full" labelClass="gap-2 md:gap-0" label="News & Updates" />
                
                <x-form.toggle wrapClass="h-full" labelClass="gap-2 md:gap-0" label="Travel Insights Subscriptions" />
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
