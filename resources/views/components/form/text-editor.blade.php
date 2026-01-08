@props([
  'label' => '',
  'placeholder' => '',
  'id' => 'id'.uniqid(),
  'image' => true,
  'tooltip' => false,
  'tooltipText' => null,
  'max' => 10000,
])
        
<div 
  class="relative !text-sm sm:!text-base text-editor"
  x-data="{
    len: 0,
    max: {{ $max }},
    updateLen() {
      const editorEl = document.querySelector('[data-model=\'{{ $id }}\']');
      if (editorEl) {
        const qlEditor = editorEl.querySelector('.ql-editor');
        if (qlEditor) {
          // Считаем все символы внутри div.ql-editor
          const text = qlEditor.innerText || qlEditor.textContent || '';
          this.len = text.length;
        }
      }
    },
    initCounter() {
      const checkQuill = () => {
        const editorEl = document.querySelector('[data-model=\'{{ $id }}\']');
        if (editorEl) {
          const qlEditor = editorEl.querySelector('.ql-editor');
          if (qlEditor) {
            this.updateLen();
            // Слушаем изменения через MutationObserver для отслеживания изменений в div.ql-editor
            const observer = new MutationObserver(() => {
              this.updateLen();
            });
            observer.observe(qlEditor, {
              childList: true,
              subtree: true,
              characterData: true
            });
            // Также слушаем события ввода
            qlEditor.addEventListener('input', () => {
              this.updateLen();
            });
            qlEditor.addEventListener('paste', () => {
              setTimeout(() => this.updateLen(), 10);
            });
          } else {
            setTimeout(checkQuill, 100);
          }
        } else {
          setTimeout(checkQuill, 100);
        }
      };
      setTimeout(checkQuill, 300);
    }
  }"
  x-init="() => {
    initCounter();
    Livewire.hook('morphed', () => {
      setTimeout(() => {
        const editorEl = document.querySelector('[data-model=\'{{ $id }}\']');
        if (editorEl) {
          updateLen();
        }
      }, 100);
    });
    Livewire.hook('message.processed', () => {
      setTimeout(() => {
        const editorEl = document.querySelector('[data-model=\'{{ $id }}\']');
        if (editorEl) {
          updateLen();
        }
      }, 100);
    });
  }"
  >
  <textarea {{ $attributes }} id="{{ $id }}" class="!w-0 !h-0 !opacity-0 absolute"></textarea>
  <div class="!mb-2 text-gray">{{ $label }}</div>
  <div 
    class="quill-editor !bg-light !border-none !rounded-lg grid grid-cols-1
      !pr-4 !text-sm sm:!text-base overflow-auto scrollbar-custom
      @if($attributes->get('name')) @error($attributes->get('name')) !border !border-red-500 @enderror @endif" 
    data-placeholder="{{ $placeholder }}"
    data-model="{{ $id }}"
    data-image="{{ $image ? 'true' : 'false' }}"
    data-max="{{ $max }}"
    style="min-height: 400px;"
  >
  </div>
  
  <div class="text-sm !text-gray text-right mt-2">
    <span x-html="len"></span>
    <span>/</span>
    <span x-html="max"></span>
  </div>
  @if($tooltip && filled($tooltipText))
    <x-tooltip class="!right-3 !top-32 xs:!top-25" :message="$tooltipText"></x-tooltip>
  @endif

  @if ($attributes->get('name'))
    @error($attributes->get('name'))
      <div class="!mt-2 text-red-500">{{ $message }}</div>
    @enderror
  @endif
</div>
<style>
  /* Стили для списков в Quill редакторе */
  .quill-editor .ql-editor ul,
  .quill-editor .ql-editor ol {
    list-style-position: outside !important;
    padding-left: 30px !important;
    margin: 15px 0 !important;
  }
  .quill-editor .ql-editor ul {
    list-style-type: disc !important;
    list-style: disc outside !important;
  }
  .quill-editor .ql-editor ol {
    list-style-type: decimal !important;
    list-style: decimal outside !important;
  }
  .quill-editor .ql-editor li {
    display: list-item !important;
    list-style-position: outside !important;
    margin: 2px 0 !important; /* Уменьшено с 8px до 2px для более компактных списков */
    padding-left: 5px !important;
    line-height: 1.5 !important; /* Немного уменьшено для более компактного вида */
  }
  
  /* Увеличиваем расстояние между абзацами */
  .quill-editor .ql-editor p {
    margin: 20px 0 !important; /* Больше пространства между параграфами */
    line-height: 1.6 !important;
  }
  
  /* Для пустых параграфов уменьшаем margin */
  .quill-editor .ql-editor p:empty {
    margin: 8px 0 !important;
  }
  .quill-editor .ql-editor ul ul {
    list-style-type: circle !important;
    list-style: circle outside !important;
  }
  .quill-editor .ql-editor ul ul ul {
    list-style-type: square !important;
    list-style: square outside !important;
  }
  /* Убираем курсив у placeholder */
  .quill-editor .ql-editor.ql-blank::before {
    font-style: normal !important;
  }
  /* Устанавливаем минимальную высоту для редактора */
  .quill-editor .ql-editor {
    min-height: 400px !important;
  }
  
  /* Сохраняем все inline стили из Word - они будут применяться напрямую */
  /* Quill будет сохранять все стили из атрибута style при вставке */
  .quill-editor .ql-editor [style] {
    /* Все стили из Word будут сохранены в атрибуте style и применятся автоматически */
  }
  
  /* Убеждаемся, что параграфы сохраняют свои стили */
  .quill-editor .ql-editor p[style],
  .quill-editor .ql-editor div[style] {
    /* Стили из атрибута style применяются автоматически */
  }
  
  /* Поддержка отступов из Word */
  .quill-editor .ql-editor p[style*="text-indent"],
  .quill-editor .ql-editor p[style*="margin-left"],
  .quill-editor .ql-editor p[style*="padding-left"] {
    /* Отступы из Word сохраняются через style атрибут */
  }
  
  /* Поддержка разных размеров шрифта */
  .quill-editor .ql-editor [style*="font-size"] {
    /* Размеры шрифта сохраняются */
  }
</style>