@props([
  'id' => 'id'.uniqid(),
  'placeholder' => '',
  'label' => null,
  'type' => 'media',
  'wrapClass' => '',
  'filename' => null,
  'fullWidth' => false,
  'imageUrl' => null,
  'width' => null, // '50%', '100%', etc.
  'onRemove' => null, // Livewire method name to call on remove
])

<div 
  class="{{ ($filename || $fullWidth || $imageUrl) ? ($width ? '' : 'w-full') : '' }}"
  style="{{ ($filename || $fullWidth || $imageUrl) ? ($width ? "width: {$width};" : 'width: 100%;') : '' }}"
  x-data="{
    isDragging: false,
    handleDrop(evt) {
      evt.preventDefault();
      this.isDragging = false;
      
      const files = evt.dataTransfer.files;
      
      // Если это перетаскивание файла, обрабатываем его
      if (files.length > 0) {
        evt.stopPropagation(); // Останавливаем всплытие только для файлов
        const file = files[0];
        const input = this.$refs.file;
        
        // Проверяем accept атрибут, если он есть
        const accept = input.getAttribute('accept');
        if (accept && accept !== '*/*') {
          const acceptedTypes = accept.split(',').map(t => t.trim());
          const fileType = file.type;
          const fileName = file.name.toLowerCase();
          
          const isAccepted = acceptedTypes.some(type => {
            if (type.startsWith('.')) {
              return fileName.endsWith(type);
            }
            if (type.endsWith('/*')) {
              const baseType = type.split('/')[0];
              return fileType.startsWith(baseType + '/');
            }
            return fileType === type;
          });
          
          if (!isAccepted) {
            return;
          }
        }
        
        // Создаем DataTransfer и устанавливаем файл
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        
        // Вызываем событие change для Livewire
        const changeEvent = new Event('change', { bubbles: true });
        input.dispatchEvent(changeEvent);
        
        // Также вызываем input событие для совместимости
        const inputEvent = new Event('input', { bubbles: true });
        input.dispatchEvent(inputEvent);
      }
    },
    handleDragOver(evt) {
      evt.preventDefault();
      evt.stopPropagation();
      if (!this.isDragging) {
        this.isDragging = true;
      }
    },
    handleDragEnter(evt) {
      evt.preventDefault();
      evt.stopPropagation();
      this.isDragging = true;
    },
    handleDragLeave(evt) {
      evt.preventDefault();
      evt.stopPropagation();
      // Проверяем, что мы действительно покинули элемент, а не перешли на дочерний
      const rect = evt.currentTarget.getBoundingClientRect();
      const x = evt.clientX;
      const y = evt.clientY;
      if (x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) {
        this.isDragging = false;
      }
    }
  }"
  class="relative"
  x-on:dragover.prevent="handleDragOver"
  x-on:dragenter.prevent="handleDragEnter"
  x-on:dragleave.prevent="handleDragLeave"
  x-on:drop.prevent="handleDrop"
>
  <input x-ref="file" type="file" id="{{ $id }}" class="w-0 h-0 opacity-0 absolute" {{ $attributes }}>
  @if($label)
    <div x-on:click.prevent="() => $refs.file.click()" class="!text-gray !mb-2 hover:cursor-pointer">{{ $label }}</div>
  @endif
  <div 
    x-bind:class="isDragging ? 'ring-2 ring-active bg-active/10 border-2 border-dashed border-active' : ''"
    class="rounded-lg bg-light text-gray transition relative block overflow-hidden
    @if($attributes->get('name')) @error($attributes->get('name')) !border !border-red-500 @enderror @endif"
    style="{{ ($filename || $fullWidth || $imageUrl || $width) ? 'width: 100%; min-height: 100px; padding: 12px;' : 'width: 100px; height: 100px; min-height: 100px; padding: 12px;' }} display: flex; align-items: center; justify-content: center;"
    x-on:dragover.prevent="handleDragOver"
    x-on:dragenter.prevent="handleDragEnter"
    x-on:dragleave.prevent="handleDragLeave"
    x-on:drop.prevent="handleDrop"
    >
      @if($imageUrl)
        <img src="{{ $imageUrl }}" alt="Preview" class="absolute inset-0 w-full h-full object-cover rounded-lg z-0">
        <div class="absolute inset-0 bg-black/20 z-10 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity rounded-lg pointer-events-none">
          <div class="text-white text-sm font-medium">Click to change image</div>
        </div>
        @if($onRemove)
          <button 
            type="button"
            wire:click="{{ $onRemove }}"
            wire:loading.attr="disabled"
            x-on:click.stop.prevent="() => {}"
            class="absolute top-2 right-2 z-[9999] bg-white rounded-full p-1.5 shadow-lg hover:bg-red-500 hover:text-white transition-colors cursor-pointer"
            title="Remove image"
          >
            @include('icons.close', ['width' => 16, 'height' => 16])
          </button>
        @endif
      @endif

      {{ $slot }}

      <div class="flex {{ ($filename || $fullWidth || $imageUrl || $width) ? 'justify-start' : 'justify-center' }} items-center !gap-2 {{ $wrapClass }} {{ ($filename || $fullWidth || $imageUrl || $width) ? 'relative' : 'absolute inset-0' }} z-50" style="display: flex; align-items: center; {{ ($filename || $fullWidth || $imageUrl || $width) ? 'padding: 0;' : 'justify-content: center;' }}">
        @if(!$imageUrl)
        <div 
          x-show="!isDragging"
          x-on:click.prevent="() => $refs.file.click()"
            class="@if($filename || $fullWidth || $width) flex items-center !gap-2 w-full hover:cursor-pointer hover:text-active @else flex justify-center items-center !gap-2 hover:cursor-pointer hover:text-active @endif"
        >
          @if($type == 'file')
            @include('icons.document', ['width' => 32, 'height' => 32])
          @else
            @include('icons.download', ['width' => 32, 'height' => 32])
          @endif

          @if($filename)
            <div class="flex-1 truncate">{{ $filename }}</div>
          @endif
        </div>
        @else
          <div 
            x-show="!isDragging"
            x-on:click.prevent="(e) => { 
              const button = e.target.closest('button[wire\\:click]');
              if (!button) {
                $refs.file.click();
              }
            }"
            class="absolute inset-0 w-full h-full cursor-pointer z-20"
            style="pointer-events: auto;"
          ></div>
        @endif
        <div x-show="isDragging" class="flex flex-col items-center justify-center !gap-2 text-active font-medium z-30 relative">
          <div class="text-sm">Drop file here</div>
        </div>
        @if($placeholder && !$filename && !$imageUrl)
          <div class="" x-show="!isDragging">{{ $placeholder }}</div>
        @endif
      </div>
    </div>
    @if($attributes->get('name'))
      @error($attributes->get('name'))
        <div class="!mt-2 text-red-500">{{ $message }}</div>
      @enderror
    @endif
    {{ $drop ?? '' }}
</div>