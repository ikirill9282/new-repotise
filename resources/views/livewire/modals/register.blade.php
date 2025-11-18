<div class="relative">
  <h2 class="text-2xl font-bold text-gray-900 !mb-6 text-center select-none">Sign Up</h2>
  
  <form wire:submit="submit" class="!space-y-4 !mb-10">
    @csrf
    
    <x-form.input wire:model="form.email" name="email" type="email" :tooltipModal="true" />

    <div x-data="{ type: 'password' }" class="">
      <x-form.input wire:model="form.password" name="password" x-bind:type="type" placeholder="Password" :tooltipModal="true" tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols.">
        <x-slot name="icon">
          <div x-on:click="() => type = (type == 'password') ? 'text' : 'password' " class="absolute top-1/2 right-9 translate-y-[-50%] hover:cursor-pointer">
            <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
          </div>
        </x-slot>
      </x-form.input>
    </div>

    <div x-data="{ type: 'password' }" class="">
      <x-form.input wire:model="form.repeat_password" name="repeat_password" x-bind:type="type" placeholder="Create a password" :tooltipModal="true" tooltipText="Password must be at least 8 characters long and include a mix of letters, numbers, and symbols.">
        <x-slot name="icon">
          <div x-on:click="() => type = (type == 'password') ? 'text' : 'password' " class="absolute top-1/2 right-9 translate-y-[-50%] hover:cursor-pointer">
            <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
          </div>
        </x-slot>
      </x-form.input>
    </div>

    <div class="!mb-10">
      <x-form.checkbox wire:model="form.as_seller" name="as_seller" label="Sign up as a seller" />
    </div>

    <div class="flex justify-between items-center !gap-2">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="basis-1/3" gray>Cancel</x-btn>
      <x-btn wire:click.prevent="attempt" class="basis-2/3" >Sign Up</x-btn>
    </div>

    <div class="flex items-center justify-center group text-gray">
      Already have an account? <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'auth' })" href="#" class="!border-0 !inline-bliock !p-0 ml-1 group-has-[a]:!text-active">Sign In</x-link>
    </div>
  </form>

  <div class="flex justify-center items-center !gap-2 !mb-6">
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
    <div class="text-gray shrink-0 text-sm">Other log in options.</div>
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
  </div>

  <div class="flex justify-between items-center !gap-2 text-gray !mb-6">
    <div wire:click.prevent="googleAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/google.svg') }}" alt="Google"></div>
      <div class="transition group-hover:text-active !mt-0.5">Google</div>
    </div>
    <div class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/facebook.svg') }}" alt="Facebook"></div>
      <div class="transition group-hover:text-active !mt-0.5">Facebook</div>
    </div>
    <div class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/xai.svg') }}" alt="XAI"></div>
      <div class="transition group-hover:text-active !mt-0.5">X (Twitter)</div>
    </div>
  </div>

  <div class="group text-sm text-gray">
    By clicking ‘‘Sign Up,’’ you agree to our <x-link href="/policies/terms-and-conditions" target="_blank" class="!border-0 group-has-[a]:!text-active">Terms of Service</x-link>, <x-link href="/policies/privacy-policy" target="_blank" class="!border-0 group-has-[a]:!text-active">Privacy Policy</x-link>, and <x-link href="/policies" target="_blank" class="!border-0 group-has-[a]:!text-active">Other Terms</x-link>.
  </div>
</div>