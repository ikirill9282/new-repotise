<div class="">
    <div class="overflow-x-scroll scrollbar-custom mb-4">
      <div class="flex justify-start items-center rounded-lg flex-wrap sm:!flex-nowrap">
        @foreach($this->tables as $table)
          <div 
            wire:click="setActive('{{ $table['name'] }}')" 
            class="text-center w-full text-nowrap transition
              px-1 lg:px-2.5 py-2.5 text-sm sm:text-[10px] lg:text-sm
              hover:cursor-pointer hover:bg-second hover:text-light
              basis-1/2 last:basis-full sm:basis-auto last:sm:basis-auto
              border-second sm:border-r-1 sm:border-t border-b
              sm:first:!border-l-1 sm:first:rounded-tl-lg sm:first:rounded-bl-lg sm:last:rounded-tr-lg sm:last:rounded-br-lg sm:last:rounded-bl-none
              border-r odd:border-l sm:odd:border-l-0 firs:border-l last:border-r last:rounded-br-lg last:rounded-bl-lg
              [&:nth-child(2)]:border-t [&:nth-child(1)]:border-t [&:nth-child(2)]:rounded-tr-lg [&:nth-child(1)]:rounded-tl-lg
              sm:[&:nth-child(2)]:rounded-tr-none
              @if($this->activeTable == $table['name']) bg-second text-light @endif
            "
          >
            {{ $table['title'] }}
          </div>
        @endforeach
      </div>
    </div>

    <x-card>
      @livewire("profile.tables.". $this->activeTable, key($this->activeTable))
    </x-card>
</div>
