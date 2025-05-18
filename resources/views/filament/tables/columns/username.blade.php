@php
  $record = $getRecord();    
@endphp
<div class="flex justify-start items-center gap-2 py-2">
  <div class="flex justify-center items-center">
    <img src="{{ $record->avatar }}" alt="Avatar" class="w-14 h-14 object-cover rounded-full">
  </div>
  <div class="flex flex-col">
    <div class="flex items-center justify-start gap-1">
        <x-filament::icon icon="heroicon-m-user-circle" class="h-4 w-4 text-gray-500 dark:text-gray-400" />
        <span class="text-base font-bold">{{ $record->name }}</span>
    </div>
    <div class="flex items-center justify-start gap-1">
      <x-filament::icon icon="heroicon-m-at-symbol" class="h-4 w-4 text-gray-500 dark:text-gray-400" />
      <span class="">{{ $record->username }}</span>
    </div>
    <div class="flex items-center justify-start gap-1">
      <x-filament::icon icon="heroicon-m-envelope" class="h-4 w-4 text-gray-500 dark:text-gray-400" />
      <span class="">{{ $record->email }}</span>
    </div>
</div>
</div>
