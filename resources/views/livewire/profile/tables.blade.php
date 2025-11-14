<div class="">
    <div 
      class="overflow-x-scroll scrollbar-custom mb-4 flex items-center justify-between
        @if($this->sortable) flex-col sm:flex-row !items-start sm:!items-center !gap-4 sm:!gap-0 scrollbar-custom-white @endif
      "
      >
      <div 
        class="flex justify-start items-center rounded-lg @if (count($this->tables) > 2)  flex-wrap sm:!flex-nowrap @endif">
        @foreach($this->tables as $table)
          <div 
            wire:click="setActive('{{ $table['name'] }}')" 
            class="text-center text-nowrap transition
              !px-1 lg:!px-2.5 !py-2.5 text-sm
              hover:cursor-pointer hover:bg-second hover:text-light
              basis-1/2 last:basis-full sm:basis-auto last:sm:basis-auto
              border-second sm:border-r-1 sm:border-t border-b
              sm:first:!border-l-1 sm:first:rounded-tl-lg sm:first:rounded-bl-lg sm:last:rounded-tr-lg sm:last:rounded-br-lg sm:last:rounded-bl-none
              border-r odd:border-l sm:odd:border-l-0 firs:border-l last:border-r last:rounded-bl-lg
              [&:nth-child(2)]:border-t [&:nth-child(1)]:border-t [&:nth-child(2)]:rounded-tr-lg [&:nth-child(1)]:rounded-tl-lg
              @if (count($this->tables) > 2) 
                sm:[&:nth-child(2)]:rounded-tr-none
              @else
                first:!rounded-bl-lg
                last:!rounded-br-lg last:!rounded-bl-none
              @endif
              @if($this->activeTable == $table['name']) bg-second text-light @endif
            "
          >
            <span>
              {{ $table['title'] }}
            </span>
          </div>
        @endforeach
      </div>
      @if($this->sortable && !empty($this->sortingOptions))
        <div class="sm:ml-auto">
          <label class="text-gray" for="table-sorting-select">Sort By:</label>
          <select
            class="tg-select"
            wire:model.live="sorting"
            id="table-sorting-select"
            >
            @foreach($this->sortingOptions as $value => $label)
              <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>
      @endif
    </div>

    @if(view()->exists("livewire.".$this->getTableName()))
      @php
        $childArguments = array_merge(
          ['active' => $this->activeTable, 'sorting' => $this->sorting],
          $this->args ?? []
        );
        $argsSignature = !empty($this->args)
          ? md5(json_encode($this->args))
          : 'noargs';
      @endphp
      @livewire(
        $this->getTableName(),
        $childArguments,
        key($this->activeTable . '-' . ($this->sorting ?? 'default') . '-' . $argsSignature)
      )
    @endif
</div>
