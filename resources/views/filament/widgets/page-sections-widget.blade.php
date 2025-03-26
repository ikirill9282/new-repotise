<x-filament-widgets::widget>
    <div class="flex flex-col gap-4 mb-4">
      {{-- {{ $this->header_actions }} --}}
      <h3 class="font-bold text-xl">Assign sections to page</h3>
      <x-filament-panels::form>
        {{ $this->form }}
      </x-filament-panels::form>
    </div>
    
    @if(isset($this->record) && isset($this->record->sections) && $this->record->sections->isNotEmpty())
      <div class="">
        {{ $this->table }}
      </div>
    @endif
    <x-filament-actions::modals />
</x-filament-widgets::widget>