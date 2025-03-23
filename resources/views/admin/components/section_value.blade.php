@php
  $record = $getRecord();
  // \Filament\Support\Facades\FilamentAsset::register([
  //   Filament\Support\Assets\Js::make('../../js/filament/forms/components/select.js'),
  // ])
@endphp


<x-filament-forms::field-wrapper.label class="mb-2">
    Value
</x-filament-forms::field-wrapper.label>

@if ($record->name == 'heading')

    <x-filament::input.wrapper>
        <x-filament::input.select wire:model="{{ $getStatePath() }}">
            @include('admin.components.heading', ['active' => $getState()])
        </x-filament::input.select>
    </x-filament::input.wrapper>
@elseif (str_contains($record->name, '_id'))
    @if (preg_match('/^.*_ids$/is', $record->name))
      @php
      @endphp
       {{-- <x-filament::input.wrapper>
        <x-filament::input.select
           wire:model="{{ $getStatePath() }}"
           x-muliple="true" 
          >
          @include('admin.components.heading', ['active' => $getState()])
        </x-filament::input.select>
       </x-filament::input.wrapper> --}}
       
    @else
    @endif
@else
    <x-filament::input.wrapper>
        <x-filament::input type="text" wire:model="{{ $getStatePath() }}" value={{ $getState() }} />
    </x-filament::input.wrapper>

@endif

<script src="{{ asset('/assets/js/select.js') }}"></script>