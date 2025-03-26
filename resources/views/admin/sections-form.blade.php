<x-filament-widgets::widget>
    {{-- <x-filament-panels::form id="form" :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
      wire:submit="create"> --}}
    <x-filament::fieldset class="mb-4">
        <x-slot name="label">
            Search variables
        </x-slot>

        {{ $this->form }}
    </x-filament::fieldset>

    @php
      $selected_section = $this->getSelectedSection();
    @endphp
    {{-- @dd($this); --}}
    @if($selected_section && $this->config['details'])
      <x-filament::section>
          <x-slot name="heading">
              Section: <span class="text-amber-600 dark:text-amber-400">{{ ucfirst(str($selected_section->title)->camel()) }} #{{ $selected_section->id }}</span>
          </x-slot>
          <x-slot name="description">
            Created: {{ $selected_section->created_at }}
          </x-slot>
          <div class="flex flex-col">
            @foreach ($selected_section->getAttributes() as $name => $value)
                @if (in_array($name, ['created_at', 'updated_at'])) @continue @endif
                <div class="">
                  <span class="col-span-1">{{ ucfirst($name) }}:</span>
                  <span class="col-span-1 text-amber-600 dark:text-amber-400">{{ $value }}</span>
                </div>
            @endforeach
          </div>
          {{-- @foreach ($selected_section->getAttributes()) --}}
          {{-- Content --}}
      </x-filament::section>
    @endif
    {{-- <x-filament::icon-button icon="heroicon-m-plus" wire:click="openNewUserModal" label="New label" /> --}}

    {{-- <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" /> --}}
    {{-- </x-filament-panels::form> --}}
</x-filament-widgets::widget>
