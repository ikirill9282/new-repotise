@props([
  'id' => 'id'.uniqid(),
  'placeholder' => '',
  'label' => null,
  'type' => 'media',
  'wrapClass' => '',
  'filename' => null,
])

<div 
  class="{{ $filename ? 'w-full' : '' }}"
  style="{{ $filename ? 'width: 100%;' : '' }}"
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
  class="relative hover:cursor-pointer"
  x-on:dragover.prevent="handleDragOver"
  x-on:dragenter.prevent="handleDragEnter"
  x-on:dragleave.prevent="handleDragLeave"
  x-on:drop.prevent="handleDrop"
>
  <input x-ref="file" type="file" id="{{ $id }}" class="w-0 h-0 opacity-0 absolute" {{ $attributes }}>
  @if($label)
    <div x-on:click.prevent="() => $refs.file.click()" class="!text-gray !mb-2 hover:cursor-pointer">{{ $label }}</div>
  @endif
  <label 
    for="{{ $id }}"
    x-bind:class="isDragging ? 'ring-2 ring-active bg-active/10 border-2 border-dashed border-active' : ''"
    class="rounded-lg bg-light group group-hover:cursor-pointer text-gray transition group-hover:text-active relative block"
    style="{{ $filename ? 'width: 100%; min-height: 100px; padding: 12px;' : 'width: 100px; height: 100px; min-height: 100px; padding: 12px;' }} display: flex; align-items: center; justify-content: center;"
    >

      {{ $slot }}

      <div class="flex {{ $filename ? 'justify-start' : 'justify-center' }} items-center !gap-2 {{ $wrapClass }} group-hover:cursor-pointer group-hover:text-active {{ $filename ? 'relative' : 'absolute inset-0' }} z-50" style="display: flex; align-items: center; {{ $filename ? 'padding: 0;' : 'justify-content: center;' }}">
        <div class="@if($filename) flex items-center !gap-2 w-full @else flex justify-center items-center !gap-2 @endif">
          @if($type == 'file')
            @include('icons.document', ['width' => 32, 'height' => 32])
          @else
            @include('icons.download', ['width' => 32, 'height' => 32])
          @endif

          @if($filename)
            <div class="flex-1 truncate">{{ $filename }}</div>
          @endif
        </div>
        @if($placeholder)
          <div class="">{{ $placeholder }}</div>
        @endif
      </div>
    </label>
    {{ $drop ?? '' }}
</div>