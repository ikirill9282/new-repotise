@php
    $record = $getRecord();   
@endphp

<div class="py-2">
  <x-filament::avatar
      src="{{ url($record->preview->image) }}"
      alt="{{ $record->name}}"
      :circular="false"
      size="!w-16 !h-16"
  />
</div>