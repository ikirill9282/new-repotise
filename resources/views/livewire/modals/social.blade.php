<div>
  
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Connect Your Social Media</div>
  <div class="flex flex-col items-stretch justify-start !gap-4 pb-6 mb-4 border-b-1 border-gray/30">

    {{-- YOUTUBE --}}
    <div class="flex justify-start items-center !gap-2">
      <div class="">
        <img class="!w-10" src="{{ asset('assets/img/icons/youtube.svg') }}" alt="YouTube">
      </div>
      <div class="grow">
        <x-form.input 
          :tooltip="false" 
          inputWrapClass="!py-2"
          placeholder="Add Link..."
        />
      </div>
    </div>

    {{-- TIKTOK --}}
    <div class="flex justify-start items-center !gap-2">
      <div class="">
        <img class="!w-10" src="{{ asset('assets/img/icons/tiktok.svg') }}" alt="TikTok">
      </div>
      <div class="grow">
        <x-form.input 
          :tooltip="false" 
          inputWrapClass="!py-2"
          placeholder="Add Link..."
        />
      </div>
    </div>

    {{-- FACEBOOK --}}
    <div class="flex justify-start items-center !gap-2">
      <div class="">
        <img class="!w-10" src="{{ asset('assets/img/icons/facebook.svg') }}" alt="Facebook">
      </div>
      <div class="grow">
        <x-form.input 
          :tooltip="false" 
          inputWrapClass="!py-2"
          placeholder="Add Link..."
        />
      </div>
    </div>

    {{-- INSTAGRAM --}}
    <div class="flex justify-start items-center !gap-2">
      <div class="">
        <img class="!w-10" src="{{ asset('assets/img/icons/insta.svg') }}" alt="Instagram">
      </div>
      <div class="grow">
        <x-form.input 
          :tooltip="false" 
          inputWrapClass="!py-2"
          placeholder="Add Link..."
        />
      </div>
    </div>

    {{-- GOOGLE --}}
    <div class="flex justify-start items-center !gap-2">
      <div class="">
        <img class="!w-10" src="{{ asset('assets/img/icons/google.svg') }}" alt="Google">
      </div>
      <div class="grow">
        <x-form.input 
          :tooltip="false" 
          inputWrapClass="!py-2"
          placeholder="Add Link..."
        />
      </div>
    </div>

    {{-- XAI --}}
    <div class="flex justify-start items-center !gap-2">
      <div class="">
        <img class="!w-10" src="{{ asset('assets/img/icons/xai.svg') }}" alt="XAI">
      </div>
      <div class="grow">
        <x-form.input 
          :tooltip="false" 
          inputWrapClass="!py-2"
          placeholder="Add Link..."
        />
      </div>
    </div>

    {{-- WEB1 --}}
    <div class="flex justify-start items-center !gap-2">
      <div class="">
        <img class="!w-10" src="{{ asset('assets/img/icons/web.svg') }}" alt="Web">
      </div>
      <div class="grow">
        <x-form.input 
          :tooltip="false" 
          inputWrapClass="!py-2"
          placeholder="Add Link..."
        />
      </div>
    </div>

    {{-- WEB2 --}}
    <div class="flex justify-start items-center !gap-2">
      <div class="">
        <img class="!w-10" src="{{ asset('assets/img/icons/web.svg') }}" alt="Web">
      </div>
      <div class="grow">
        <x-form.input 
          :tooltip="false" 
          inputWrapClass="!py-2"
          placeholder="Add Link..."
        />
      </div>
    </div>

  </div>


  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="!py-2 !w-auto !px-6" gray wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!py-2 !grow" >Save</x-btn>
  </div>


</div>
