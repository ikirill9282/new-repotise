<x-filament-widgets::widget class="fi-wi-table">

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}
    <x-filament::section>
      <div class="" col-span-x="2">
        {{ $this->table }}
      </div>
      <div class="" col-span-x="2">
        {{ $this->form }}
      </div>
    </x-filament::section>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
</x-filament-widgets::widget>
