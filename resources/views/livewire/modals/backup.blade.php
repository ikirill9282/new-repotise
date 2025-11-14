<div class="relative">

  <h2 class="text-2xl font-bold text-center select-none !mb-6">Use Backup Code</h2>
  <p class="!mb-4">Please enter your Backup Reset Code to regain access to your account.</p>
  <form wire:submit="submit" class="!space-y-4">
    @csrf

    <x-form.input wire:model="form.code" name="code" placeholder="Backup Code" autocomplete="one-time-code" :tooltipModal="true" tooltipText="Enter the Backup Reset Code you saved when you enabled Two-Factor Authentication." />

    <div class="flex justify-start items-stretch gap-3 group">
      <div class="basis-1/4">
        <x-btn wire:click.prevent="$dispatch('openModal', {modalName: 'auth'})" href="#" class="group-has-[a]:!text-black group-has-[a]:!bg-transparent">
          Back
        </x-btn>
      </div>

      <div class="basis-3/4">
        <x-btn wire:click.prevent="attempt" class="">
          Reset two-factor authentication
        </x-btn>
      </div>
    </div>
  </form>

</div>