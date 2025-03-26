<x-filament-widgets::widget>
    @if ($this->config['create'])
      <x-filament::fieldset class="!mb-6">
          <x-slot name="label">
              Create variables
          </x-slot>

          {{ $this->form }}

          <div class="mt-6">
            {{ $this->createSectionVariableAction }}
          </div>
      </x-filament::fieldset>
    @endif

    {{ $this->table }}
</x-filament-widgets::widget>
