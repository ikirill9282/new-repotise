<div>
  <div class="max-w-4xl">
    
    @if($editMode)
      {{-- EDIT MODE: Show all fields --}}
      <h2 class="!font-bold !text-xl !mb-10">Edit Article</h2>
    <div class="flex flex-col justify-start items-stretch !mb-10">
      <div class="flex flex-col justify-start items-stretch !gap-6">
        
          <div class="">
            <x-form.input-counter 
              wire:model="fields.title" 
              name="title"
              label="Article Title" 
              placeholder="Enter your article title here..."
              :max="70"
              :tooltip="false"
            />
          </div>
    
        <div wire:ignore class="">
            <x-form.text-editor wire:model="fields.text" name="text" label="Article Content" placeholder="Start writing your article here..." :max="10000"></x-form.text-editor>
        </div>

        <div class="">
          <x-form.chips entangle="tags" source="tags" name="tags" label="Tags" placeholder="Search or create tags...(Up to 5)" tooltipText="Enter relevant keywords to categorize your article. Use up to 5 tags. Tags help readers find your article and improve searchability." />
        </div>
        
        @if(!$this->fields['published_at'] ?? null)
          <div class="">
            <x-form.datepicker wire:model="fields.scheduled_at" label="Publish Date" placeholder="Schedule a publication date MM/DD/YEAR" />
          </div>
        @endif
      </div>
    </div>

    {{-- BANNER --}}
    <h2 class="!font-bold !text-xl !mb-10">Featured Image</h2>
    <div class="banner-block relative !mb-10">
      
      <div wire:loading class="absolute w-full h-full bg-light/50 z-150">
        <x-loader width="60" height="60" />
      </div>

      <div class="flex flex-col justify-start items-stretch relative">
          @php
            $imageUrl = null;
            if ($this->banner) {
              $imageUrl = $this->banner->temporaryUrl();
            } elseif ($this->bannerPath) {
              $imageUrl = $this->bannerPath;
            }
          @endphp
          <x-form.file 
            wire:model="banner" 
            accept="image/*" 
            name="banner" 
            placeholder="Recommended: 1200 x 675 px (16:9)" 
            :imageUrl="$imageUrl"
            width="50%"
            onRemove="removeBanner"
          ></x-form.file>
      </div>

      @error('banner')
        <div class="!mt-2 text-red-500">{{ $message }}</div>
      @enderror
    </div>

    {{-- SEO SETTINGS --}}
    <h2 class="!font-bold !text-xl !mb-10">SEO Settings (Optional)</h2>
    <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
        <x-form.input-counter 
          name="seo_title" 
          wire:model="fields.seo_title" 
          label="Meta Title" 
          placeholder="Enter meta title (for search engines)."
          :max="70"
          :tooltip="true"
          tooltipText="Your article's headline in search results. Keep it under 60 characters and include main keywords."
        />

      <x-form.textarea-counter 
        wire:model="fields.seo_text"
          name="seo_text"
          label="Meta Description"
          placeholder="Enter meta description (for search engines)."
          :max="160"
          tooltipText="The summary shown under your title in search results. Write compelling ad copy under 160 characters to win the click."
        ></x-form.textarea-counter>
      </div>

      {{-- BUTTONS EDIT MODE --}}
      <div class="flex justify-start items-stretch !gap-2 sm:!gap-4 flex-wrap sm:!flex-nowrap">
        <x-btn wire:click.prevent="draft" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" outlined>Save as Draft</x-btn>
        <x-btn wire:click.prevent="submit" class="sm:!w-auto sm:!px-28">Publish Now</x-btn>
      </div>
    @elseif($step == 1)
      {{-- STEP 1: CONTENT --}}
      <h2 class="!font-bold !text-xl !mb-10">Create New Article</h2>
      <div class="flex flex-col justify-start items-stretch !mb-10">
        <div class="flex flex-col justify-start items-stretch !gap-6">
          
          <div class="">
            <x-form.input-counter 
              wire:model="fields.title" 
              name="title"
              label="Article Title" 
              placeholder="Enter your article title here..."
              :max="70"
              :tooltip="false"
            />
          </div>
      
          <div wire:ignore class="">
            <x-form.text-editor wire:model="fields.text" name="text" label="Article Content" placeholder="Start writing your article here..." :max="10000"></x-form.text-editor>
          </div>

          <div class="">
            <x-form.chips entangle="tags" source="tags" name="tags" label="Tags" placeholder="Search or create tags...(Up to 5)" tooltipText="Enter relevant keywords to categorize your article. Use up to 5 tags. Tags help readers find your article and improve searchability." />
          </div>
          
          @if(!$this->fields['published_at'] ?? null)
            <div class="">
              <x-form.datepicker wire:model="fields.scheduled_at" label="Publish Date" placeholder="Schedule a publication date MM/DD/YEAR" />
            </div>
          @endif
        </div>
      </div>

      {{-- BANNER --}}
      <h2 class="!font-bold !text-xl !mb-10">Featured Image</h2>
      <div class="banner-block relative !mb-10">
        
        <div wire:loading class="absolute w-full h-full bg-light/50 z-150">
          <x-loader width="60" height="60" />
        </div>

        <div class="flex flex-col justify-start items-stretch relative">
          @php
            $imageUrl = null;
            if ($this->banner) {
              $imageUrl = $this->banner->temporaryUrl();
            } elseif ($this->bannerPath) {
              $imageUrl = $this->bannerPath;
            }
          @endphp
          <x-form.file 
            wire:model="banner" 
            accept="image/*" 
            name="banner" 
            placeholder="Recommended: 1200 x 675 px (16:9)" 
            :imageUrl="$imageUrl"
            width="50%"
            onRemove="removeBanner"
          ></x-form.file>
        </div>

        @error('banner')
          <div class="!mt-2 text-red-500">{{ $message }}</div>
        @enderror
      </div>

      {{-- BUTTONS STEP 1 --}}
      <div class="flex justify-start items-stretch !gap-2 sm:!gap-4 flex-wrap sm:!flex-nowrap">
        <x-btn wire:click.prevent="draft" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" outlined>Save as Draft</x-btn>
        <x-btn wire:click.prevent="nextStep" class="!max-w-none sm:!max-w-sm">Save & Continue</x-btn>
      </div>
    @else
      {{-- STEP 2: SEO SETTINGS --}}
      <h2 class="!font-bold !text-xl !mb-10">SEO Settings (Optional)</h2>
      <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
        <x-form.input-counter 
          name="seo_title" 
          wire:model="fields.seo_title" 
          label="Meta Title" 
          placeholder="Enter meta title (for search engines)."
          :max="70"
          :tooltip="true"
          tooltipText="Your article's headline in search results. Keep it under 60 characters and include main keywords."
        />

        <x-form.textarea-counter 
          wire:model="fields.seo_text"
          name="seo_text"
        label="Meta Description"
        placeholder="Enter meta description (for search engines)." 
          :max="160"
          tooltipText="The summary shown under your title in search results. Write compelling ad copy under 160 characters to win the click."
      ></x-form.textarea-counter>
    </div>

      {{-- BUTTONS STEP 2 --}}
      <div class="flex justify-start items-stretch !gap-2 sm:!gap-4 flex-wrap sm:!flex-nowrap">
        <x-btn wire:click.prevent="prevStep" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" gray>Back</x-btn>
        <x-btn wire:click.prevent="draft" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" outlined>Save as Draft</x-btn>
        <x-btn wire:click.prevent="submit" class="sm:!w-auto sm:!px-28">Publish Now</x-btn>
    </div>
    @endif
  </div>
</div>

<script>
  document.addEventListener('livewire:init', () => {
    Livewire.on('stepChanged', function() {
      if (typeof window.jQuery !== 'undefined' && window.jQuery) {
        window.jQuery(window).scrollTop(0);
      } else {
        window.scrollTo(0, 0);
      }
    });

    // Функция для прокрутки к первому полю с ошибкой
    function scrollToFirstError() {
      // Функция для поиска и прокрутки к ошибке
      const findAndScroll = () => {
        let errorField = null;
        const formContainer = document.querySelector('.max-w-4xl');
        
        if (!formContainer) return false;
        
        // Приоритет 1: Ищем первое поле с красной рамкой (border-red-500) в форме
        const fieldsWithBorder = formContainer.querySelectorAll('.border-red-500');
        if (fieldsWithBorder.length > 0) {
          // Находим первое поле по порядку в DOM
          errorField = fieldsWithBorder[0];
        }
        
        // Приоритет 2: Ищем по сообщениям об ошибках
        if (!errorField) {
          const errorMessages = formContainer.querySelectorAll('.text-red-500');
          for (let errorMsg of errorMessages) {
            if (errorMsg.textContent.trim()) {
              // Ищем родительский контейнер поля, поднимаясь вверх по DOM
              let container = errorMsg.parentElement;
              
              while (container && container !== formContainer && container !== document.body) {
                // Проверяем, есть ли в контейнере поле ввода
                const hasInput = container.querySelector('input:not([type=hidden]), textarea, select, .quill-editor, .ql-editor');
                const hasName = container.querySelector('[name]');
                
                // Проверяем наличие wire:model атрибута
                let hasWireModel = false;
                const allElements = container.querySelectorAll('*');
                const wireModelAttr = 'wire:model';
                for (let el of allElements) {
                  if (el.hasAttribute && el.hasAttribute(wireModelAttr)) {
                    hasWireModel = true;
                    break;
                  }
                  if (el.getAttribute && el.getAttribute(wireModelAttr)) {
                    hasWireModel = true;
                    break;
                  }
                }
                
                if (hasInput || hasName || hasWireModel) {
                  errorField = container;
                  break;
                }
                container = container.parentElement;
              }
              
              if (errorField) break;
            }
          }
        }
        
        // Если нашли поле с ошибкой, прокручиваем к нему
        if (errorField) {
          // Находим конкретный элемент для прокрутки
          let scrollTarget = errorField;
          
          // Для текстового редактора Quill - прокручиваем к контейнеру редактора
          const quillContainer = errorField.querySelector('.quill-editor, [data-model]');
          if (quillContainer) {
            scrollTarget = quillContainer;
          }
          
          // Вычисляем позицию элемента относительно документа
          // offsetTop дает позицию относительно offsetParent, нужно подняться до body
          let elementTop = 0;
          let element = scrollTarget;
          while (element && element !== document.body) {
            elementTop += element.offsetTop;
            element = element.offsetParent;
          }
          
          // Альтернативный способ через getBoundingClientRect
          const rect = scrollTarget.getBoundingClientRect();
          const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
          const targetScrollTop = rect.top + currentScrollTop - 120; // 120px отступ сверху
          
          // Используем более точный расчет
          const finalScrollTop = Math.max(0, targetScrollTop);
          
          // Прокручиваем к вычисленной позиции
          window.scrollTo({
            top: finalScrollTop,
            behavior: 'smooth'
          });
          
          // Фокусируемся на поле ввода после прокрутки
          setTimeout(() => {
            // Для текстового редактора Quill
            const quillEditor = errorField.querySelector('.ql-editor[contenteditable=true]');
            if (quillEditor) {
              quillEditor.focus();
              return;
            }
            
            // Для обычных полей ввода
            const input = errorField.querySelector('input:not([type=hidden]), textarea, select');
            if (input && input.type !== 'hidden') {
              input.focus();
            }
          }, 600);
          
          return true;
        }
        
        return false;
      };
      
      // Пробуем найти ошибку сразу
      if (findAndScroll()) {
        return;
      }
      
      // Если не нашли, ждем и пробуем еще раз
      setTimeout(() => {
        if (!findAndScroll()) {
          // Последняя попытка через еще немного времени
          setTimeout(findAndScroll, 200);
        }
      }, 300);
    }

    // Флаг для предотвращения множественных вызовов
    let isScrolling = false;

    // Слушаем события ошибок валидации от Livewire
    Livewire.hook('message.failed', () => {
      if (!isScrolling) {
        isScrolling = true;
        scrollToFirstError();
        setTimeout(() => { isScrolling = false; }, 1000);
      }
    });

    // Слушаем обновления после обработки сообщения
    Livewire.hook('message.processed', (message) => {
      // Проверяем, есть ли ошибки в ответе
      if (message.response && message.response.serverMemo && message.response.serverMemo.errors) {
        const errors = message.response.serverMemo.errors;
        if (Object.keys(errors).length > 0 && !isScrolling) {
          isScrolling = true;
          scrollToFirstError();
          setTimeout(() => { isScrolling = false; }, 1000);
        }
      }
    });

    // Также слушаем обновления DOM после морфинга (когда Livewire обновляет DOM)
    Livewire.hook('morphed', () => {
      // Проверяем наличие ошибок в DOM
      const hasErrors = document.querySelector('.border-red-500, .text-red-500');
      if (hasErrors && !isScrolling) {
        isScrolling = true;
        scrollToFirstError();
        setTimeout(() => { isScrolling = false; }, 1000);
      }
    });

    // Используем MutationObserver для отслеживания появления ошибок в DOM
    const observer = new MutationObserver((mutations) => {
      let hasNewErrors = false;
      
      mutations.forEach((mutation) => {
        if (mutation.type === 'childList') {
          mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) { // Element node
              // Проверяем, добавлен ли элемент с ошибкой
              if (node.classList && (node.classList.contains('text-red-500') || node.classList.contains('border-red-500'))) {
                hasNewErrors = true;
              }
              // Проверяем дочерние элементы
              if (node.querySelector && (node.querySelector('.text-red-500') || node.querySelector('.border-red-500'))) {
                hasNewErrors = true;
              }
            }
          });
        }
        
        // Проверяем изменения атрибутов (например, добавление класса border-red-500)
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          const target = mutation.target;
          if (target.classList && (target.classList.contains('border-red-500') || target.querySelector('.text-red-500'))) {
            hasNewErrors = true;
          }
        }
      });
      
      if (hasNewErrors && !isScrolling) {
        isScrolling = true;
        scrollToFirstError();
        setTimeout(() => { isScrolling = false; }, 1000);
      }
    });

    // Начинаем наблюдение за изменениями в DOM
    const formContainer = document.querySelector('.max-w-4xl');
    if (formContainer) {
      observer.observe(formContainer, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class']
      });
    }
  });
</script>
