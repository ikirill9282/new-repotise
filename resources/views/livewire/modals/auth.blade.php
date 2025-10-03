<div class="relative">
  <h2 class="text-2xl font-bold text-gray-900 !mb-6 text-center select-none">Sign In</h2>
  
  <form wire:submit="submit" class="!space-y-4 !mb-10">
    @csrf

    {{-- STEP 1 --}}
    @if($this->step == 1)

      <div class="!mb-6">
        <x-form.input wire:model="form.email" name="email" type="email" placeholder="Email"></x-form.input>
      </div>

    {{-- STEP 2 --}}
    @elseif ($this->step == 2)

      <div class="">
        <x-form.input wire:model="form.email" name="email" type="email" placeholder="Email"></x-form.input>
      </div>
      
      <div x-data="{ type: 'password' }" class="">
        <x-form.input wire:model="form.password" name="password" x-bind:type="type" placeholder="password" :tooltip="false">
          <x-slot name="icon">
            <div x-on:click="() => type = (type == 'password') ? 'text' : 'password' " class="absolute top-1/2 right-3 translate-y-[-50%] hover:cursor-pointer">
              <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
            </div>
          </x-slot>
        </x-form.input>
      </div>
      
      @if($this->getUser()?->getAttribute('2fa'))
        <div class="!mb-3">
          <x-form.input wire:model="form.2fa" name="2fa" placeholder="Authenticator App Code" />
        </div>

        <div class="">
          <x-form.checkbox label="Use Backup Code" />
        </div>
      @endif

    @endif

    <div class="flex justify-between items-center !gap-2">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="basis-1/3" gray>Cancel</x-btn>
      <x-btn wire:click.prevent="attempt" class="basis-2/3" >Continue</x-btn>
    </div>

    <div class="flex items-center justify-center">
      <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'reset-password' })" href="#" class="!border-0">Forgot password?</x-link>
    </div>
  </form>

  <div class="flex justify-center items-center !gap-2 !mb-6">
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
    <div class="text-gray shrink-0 text-sm">Other log in options.</div>
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
  </div>

  <div class="flex justify-between items-center !gap-2 text-gray">
    <div wire:click.prevent="googleAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/google.svg') }}" alt="Google"></div>
      <div class="transition group-hover:text-active !mt-0.5">Google</div>
    </div>
    <div wire:click.prevent="fbAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/facebook.svg') }}" alt="Facebook"></div>
      <div class="transition group-hover:text-active !mt-0.5">Facebook</div>
    </div>
    <div wire:click.prevent="xAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/xai.svg') }}" alt="XAI"></div>
      <div class="transition group-hover:text-active !mt-0.5">X (Twitter)</div>
    </div>
  </div>
</div>