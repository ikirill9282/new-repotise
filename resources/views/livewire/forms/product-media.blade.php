<div>
  <h1 class="!font-normal !m-0 !mb-10">Create Product - Media & Files</h1>

  @php
    $breadcrumbs = [
      'My Account' => route('profile'),
      'My Products' => route('profile.products'),
      "Create Product (2/2)" => route('profile.products.create.media'),
    ];
  @endphp
  <x-breadcrumbs class="!mb-10" :breadcrumbs="$breadcrumbs" />

  <div class="max-w-4xl">

      {{-- MEDIA --}}
      <h2 class="!font-bold !text-2xl !mb-10">Product Media & Files</h2>
      <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
        
        {{-- BANNER --}}
        <div class="relative" 
          x-data="{
            uploadProgress: 0,
            isUploading: false,
            progressInterval: null,
            checkLoading() {
              const loadingEl = this.$el.querySelector('[wire\\:loading][wire\\:target*=\'banner\']');
              const isLoading = loadingEl && window.getComputedStyle(loadingEl).display !== 'none';
              
              if (isLoading && !this.isUploading) {
                this.startProgress();
              } else if (!isLoading && this.isUploading && this.uploadProgress < 100) {
                // Если загрузка завершилась, но прогресс еще не 100, завершаем
                this.finishProgress();
              }
            },
            startProgress() {
              this.isUploading = true;
              this.uploadProgress = 0;
              
              // Плавное увеличение прогресса
              this.progressInterval = setInterval(() => {
                if (this.uploadProgress < 95) {
                  // Увеличиваем медленнее к концу
                  const increment = this.uploadProgress < 50 ? 5 : this.uploadProgress < 80 ? 3 : 1;
                  this.uploadProgress = Math.min(this.uploadProgress + increment, 95);
                }
              }, 150);
            },
            finishProgress() {
              if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
              }
              this.uploadProgress = 100;
              setTimeout(() => {
                this.isUploading = false;
                this.uploadProgress = 0;
              }, 600);
            },
            init() {
              // Проверяем состояние загрузки периодически
              setInterval(() => this.checkLoading(), 100);
              
              // Также слушаем события Livewire
              window.addEventListener('livewire:upload-progress', (event) => {
                const fieldName = event.detail?.name || event.detail?.[0];
                const progress = event.detail?.progress ?? event.detail?.[1] ?? 0;
                
                if (fieldName && fieldName.includes('banner')) {
                  if (this.progressInterval) {
                    clearInterval(this.progressInterval);
                    this.progressInterval = null;
                  }
                  this.uploadProgress = Math.round(progress);
                  this.isUploading = progress < 100;
                }
              });
              
              window.addEventListener('livewire:upload-finish', (event) => {
                const fieldName = event.detail?.name || event.detail?.[0];
                if (fieldName && fieldName.includes('banner')) {
                  this.finishProgress();
                }
              });
            }
          }"
        >
          <div class="w-full max-w-sm">
            <x-form.file wire:model="fields.banner.uploaded" label="Featured Photo" accept="image/*" wrapClass="relative z-50 transition">
              <div wire:loading wire:target="fields.banner.uploaded" class="absolute inset-0 w-full h-full bg-light/50 z-150 flex items-center justify-center rounded-lg">
                <x-loader width="60" height="60" />
              </div>

              @if($this->fields['banner']['uploaded'])
                <div class="absolute inset-0 w-full h-full !rounded-lg overflow-hidden z-40 group-hover:cursor-pointer">
                  <img class="object-cover h-full w-full !inline-block opacity-100 transition group-hover:!opacity-50" src="{{ $this->fields['banner']['uploaded']->temporaryUrl() }}" alt="Banner">
                </div>
              @elseif ($this->fields['banner']['preview'])
                <div class="absolute inset-0 w-full h-full !rounded-lg overflow-hidden z-40 group-hover:cursor-pointer">
                  <img class="object-cover h-full w-full !inline-block opacity-100 transition group-hover:!opacity-50" src="{{ $this->fields['banner']['preview'] ?? '' }}" alt="Banner">
                </div>
              @endif

              <x-slot name="drop">
                @if($this->fields['banner']['uploaded'] || $this->fields['banner']['preview'])
                  <div class="!mt-2 flex justify-center items-center" style="width: 100px;">
                    <div wire:click.prevent="dropBanner" class="text-center flex justify-center items-center hover:cursor-pointer hover:text-active">
                      @include('icons.close', ['width' => 9, 'height' => 9])
                    </div>
                  </div>
                @endif
              </x-slot>
            </x-form.file>
          </div>

          {{-- Процент загрузки под фотографией --}}
          <div x-show="isUploading && uploadProgress > 0" 
               x-cloak
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 translate-y-2"
               x-transition:enter-end="opacity-100 translate-y-0"
               x-transition:leave="transition ease-in duration-300"
               x-transition:leave-start="opacity-100 translate-y-0"
               x-transition:leave-end="opacity-0 translate-y-2"
               class="max-w-sm mb-4">
            <div style="background: linear-gradient(to right, #FC7361, #484134);" class="rounded-lg px-3 py-2 shadow-md">
              <div class="flex items-center justify-between mb-1.5">
                <span class="text-white font-medium text-xs">Uploading...</span>
                <span class="text-white font-bold text-base" x-text="Math.round(uploadProgress) + '%'"></span>
              </div>
              <div class="w-full bg-white/30 rounded-full h-1.5 overflow-hidden">
                <div class="h-full bg-white rounded-full transition-all duration-300 ease-out" 
                     x-bind:style="'width: ' + Math.round(uploadProgress) + '%'"></div>
              </div>
            </div>
          </div>

          @error('banner')
            <div class="!mt-2 text-red-500">{{ $message }}</div>
          @enderror
        </div>

        {{-- PHOTOS --}}
        <div class="">
          <div class="text-gray !mb-2">Additional Photos</div>
          <div 
            class="flex justify-start items-start !gap-2 flex-wrap"
            x-ref="galleryContainer"
            x-data="{
              draggedIndex: null,
              dragOverIndex: null,
              handleDragStart(event, index) {
                console.log('handleDragStart called', { index });
                this.draggedIndex = index;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/html', event.target);
                event.currentTarget.style.opacity = '0.5';
                event.currentTarget.style.cursor = 'grabbing';
              },
              handleDragEnd(event) {
                event.currentTarget.style.opacity = '1';
                event.currentTarget.style.cursor = '';
                this.draggedIndex = null;
                this.dragOverIndex = null;
                // Убираем визуальные эффекты со всех элементов
                event.currentTarget.parentElement.querySelectorAll('.drag-over').forEach(el => {
                  el.classList.remove('drag-over');
                });
              },
              handleDragOver(event, dropIndex) {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
                if (this.draggedIndex !== null && this.draggedIndex !== dropIndex) {
                  this.dragOverIndex = dropIndex;
                  event.currentTarget.classList.add('drag-over');
                }
              },
              handleDragLeave(event) {
                event.currentTarget.classList.remove('drag-over');
                if (this.dragOverIndex !== null) {
                  this.dragOverIndex = null;
                }
              },
              handleDrop(event, dropIndex) {
                console.log('handleDrop called', { dropIndex, draggedIndex: this.draggedIndex });
                event.preventDefault();
                event.currentTarget.classList.remove('drag-over');
                if (this.draggedIndex !== null && this.draggedIndex !== dropIndex) {
                  console.log('Conditions met, proceeding with reorder');
                  // Получаем текущие ключи из DOM через data-атрибуты
                  const container = event.currentTarget.closest('.flex.flex-wrap, .flex-wrap');
                  if (!container) {
                    console.error('Container not found');
                    return;
                  }
                  
                  const galleryItems = Array.from(container.querySelectorAll('[data-gallery-key]'));
                  const galleryKeys = galleryItems.map(item => item.getAttribute('data-gallery-key')).filter(Boolean);
                  
                  if (galleryKeys.length === 0) {
                    console.error('No gallery keys found');
                    return;
                  }
                  
                  const draggedKey = galleryKeys[this.draggedIndex];
                  const newOrder = [...galleryKeys];
                  newOrder.splice(this.draggedIndex, 1);
                  newOrder.splice(dropIndex, 0, draggedKey);
                  
                  console.log('Reordering gallery:', {
                    draggedIndex: this.draggedIndex,
                    dropIndex: dropIndex,
                    draggedKey: draggedKey,
                    newOrder: newOrder,
                    currentKeys: galleryKeys
                  });
                  
                  // Вызываем метод Livewire для обновления порядка
                  console.log('Calling reorderGallery with order:', newOrder);
                  
                  // Используем @this для вызова метода Livewire
                  @this.call('reorderGallery', newOrder)
                    .then((result) => {
                      console.log('Reorder completed successfully', result);
                      this.draggedIndex = null;
                      this.dragOverIndex = null;
                    })
                    .catch((error) => {
                      console.error('Reorder error:', error);
                      alert('Ошибка при изменении порядка: ' + (error.message || 'Неизвестная ошибка'));
                    });
                } else {
                  event.currentTarget.style.opacity = '1';
                  this.draggedIndex = null;
                  this.dragOverIndex = null;
                }
              }
            }"
          >
            @php
              // Используем порядок из galleryOrder, если он есть, иначе используем порядок из fields['gallery']
              $galleryOrder = !empty($this->galleryOrder) ? $this->galleryOrder : array_keys($this->fields['gallery']);
            @endphp
            @foreach($galleryOrder as $key)
              @php
                $item = $this->fields['gallery'][$key] ?? ['uploaded' => null, 'preview' => null, 'id' => null];
                $has_image = boolval($item['uploaded'] || $item['preview']);
                $loopIndex = $loop->index;
              @endphp

              <div 
                wire:key="gallery-{{ $key }}"
                data-gallery-key="{{ $key }}"
                data-loop-index="{{ $loopIndex }}"
                class="relative transition-all duration-200"
                draggable="{{ $has_image ? 'true' : 'false' }}"
                @dragstart="handleDragStart($event, {{ $loopIndex }})"
                @dragend="handleDragEnd($event)"
                @dragover.prevent="handleDragOver($event, {{ $loopIndex }})"
                @dragleave="handleDragLeave($event)"
                @drop.prevent="handleDrop($event, {{ $loopIndex }})"
                style="cursor: {{ $has_image ? 'grab' : 'default' }};"
                x-bind:class="dragOverIndex === {{ $loopIndex }} ? 'ring-2 ring-active scale-105' : ''"
              >

                  <x-form.file 
                    wire:model.defer="fields.gallery.{{ $key }}.uploaded" 
                    :delete="!empty($item['uploaded']) || !empty($item['preview'])"
                    accept="image/*" 
                    wrapClass="relative z-50 transition {{ $has_image ? '!text-white group-hover:!text-active' : '' }}"
                  >
                    <div wire:loading class="absolute w-full h-full top-0 left-0 bg-light/50 z-150">
                      <x-loader width="60" height="60" />
                    </div>
                    @if($item['uploaded'])
                      <div class="absolute w-full h-full top-0 left-0 !rounded-lg overflow-hidden z-40 group-hover:cursor-pointer">
                        <img class="object-cover h-full w-full !inline-block opacity-100 transition group-hover:!opacity-50" src="{{ $item['uploaded']->temporaryUrl() }}" alt="Banner">
                      </div>
                    @elseif ($item['preview'])
                      <div class="absolute w-full h-full top-0 left-0 !rounded-lg overflow-hidden z-40 group-hover:cursor-pointer">
                        <img class="object-cover h-full w-full !inline-block opacity-100 transition group-hover:!opacity-50" src="{{ $item['preview'] ?? '' }}" alt="Banner">
                      </div>
                    @endif

                    <x-slot name="drop">
                      @if($item['uploaded'] || $item['preview'])
                        <div wire:click.prevent="dropPhoto('{{ $key }}')" class="!mt-2 text-center flex justify-center items-center hover:cursor-pointer hover:text-active">
                          @include('icons.close', ['width' => 9, 'height' => 9])
                        </div>
                      @endif
                    </x-slot>
                  </x-form.file>
              </div>

            @endforeach
          </div>
        </div>

      </div>

      <h2 class="!font-bold !text-2xl !mb-10">Product Files</h2>
      <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
        
        {{-- PP TEXT --}}
        <div class="">
          <x-form.text-editor wire:model="fields.pp_text" :image="false" label="Post-Purchase Text (Optional):" placeholder="Start writing your post-purchase text here..."></x-form.text-editor>
        </div>

        {{-- PRODUCT FILES --}}
        <div class="!mb-10">
          <div class="!mb-4 !text-sm sm:!text-base">Upload up to 8 files of any file type for your product. Each file can be up to 100MB in size. You can upload a large video to platforms such as YouTube or Vimeo and embed the link below.</div>
          <div class="flex flex-col justify-start items-stretch !gap-2">
            {{-- Загруженные файлы на всю ширину --}}
            @foreach($this->fields['files'] as $key => $file)
              @php
                $filename = empty($file['uploaded']) ? (empty($file['current']) ? null : $file['current']) : $file['uploaded']->getClientOriginalName();
                $hasFile = !empty($filename);
              @endphp
              @if($hasFile)
                <div class="flex flex-col justify-center items-center hover:cursor-pointer w-full">
                  <x-form.file 
                    wire:model="fields.files.{{ $key }}.uploaded" 
                    type="file" 
                    accept="*/*"
                    :filename="$filename"
                  >
                    <div wire:loading class="absolute w-full h-full top-0 left-0 bg-light/50 z-150">
                      <x-loader width="60" height="60" />
                    </div>
                    <x-slot name="drop">
                      <div class="flex justify-center items-center !gap-6">
                        <div wire:click.prevent="$dispatch('openModal', { modalName: 'file-description', args: { filename: '{{ $filename }}', key: '{{ $key }}', description: '{{ $file['description'] ?? '' }}' } })" class="text-sm transition hover:text-active hover:cursor-pointer">+ Add a note</div>

                        <div wire:click.prevent="dropFile('{{ $key }}')" class="text-center flex justify-center items-center hover:cursor-pointer hover:text-active">
                          @include('icons.close', ['width' => 9, 'height' => 9])
                        </div>
                      </div>
                    </x-slot>
                  </x-form.file>
                </div>
              @endif
            @endforeach
          </div>
          {{-- Пустые контейнеры в ряд --}}
          <div class="flex justify-start items-start !gap-2 flex-wrap !mt-2">
            @foreach($this->fields['files'] as $key => $file)
              @php
                $filename = empty($file['uploaded']) ? (empty($file['current']) ? null : $file['current']) : $file['uploaded']->getClientOriginalName();
                $hasFile = !empty($filename);
              @endphp
              @if(!$hasFile)
                <div class="flex flex-col justify-center items-center hover:cursor-pointer">
                  <x-form.file 
                    wire:model="fields.files.{{ $key }}.uploaded" 
                    type="file" 
                    accept="*/*"
                    :filename="$filename"
                  >
                    <div wire:loading class="absolute w-full h-full top-0 left-0 bg-light/50 z-150">
                      <x-loader width="60" height="60" />
                    </div>
                  </x-form.file>
                </div>
              @endif
            @endforeach
          </div>
          <div class="text-sm text-gray !mt-2">You can add a description to each file (optional)</div>
        </div>
        

        {{-- LINKS --}}
        <div class="">
          <h2 class="!font-bold !text-2xl !mb-10 relative !inline-block !pr-6">
            Video Link (Optional)
            <x-tooltip message="Optional links to video content that will be delivered to the customer after purchase. Use this to provide access to video courses, tutorials, or exclusive video content hosted on platforms like YouTube or Vimeo."></x-tooltip>
          </h2>
          <div class="flex flex-col justify-start items-stretch !gap-2">
            @foreach ($this->fields['links'] as $key => $link)
              <div class="flex justify-between items-stretch">
                <x-form.input wire:model="fields.links.{{ $key }}.link" :tooltip="false" placeholder="Link" />
                <span wire:click="addLink" class="text-2xl !font-light !p-3 !leading-6 transition hover:cursor-pointer hover:text-active">+</span>
              </div>
            @endforeach
          </div>
        </div>

        {{-- BUTTONS --}}
        <div class="flex justify-start items-stretch !gap-2 sm:!gap-4 flex-wrap sm:!flex-nowrap">
          <x-btn wire:click.prevent="prevStep" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" gray>Back to Details</x-btn>
          <x-btn wire:click.prevent="draft" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" outlined>Save as Draft</x-btn>
          <x-btn wire:click.prevent="submit" class="!max-w-none sm:!max-w-sm">Save & Continue</x-btn>
        </div>
      </div>
  </div>
</div>
