import "./bootstrap";
import "quill/dist/quill.snow.css";

// import { autocomplete } from '@algolia/autocomplete-js'
// import {
//   meilisearchAutocompleteClient,
//   getMeilisearchResults,
// } from '@meilisearch/autocomplete-client'

// import '@algolia/autocomplete-theme-classic'

import AirDatepicker from "air-datepicker";
import "air-datepicker/air-datepicker.css";
import localeEn from "air-datepicker/locale/en";

import Quill from "quill";

window.Quill = Quill;
window.AirDatepicker = AirDatepicker;
window.createDatePicker = createDatePicker;
window.objectToQueryString = objectToQueryString;

function objectToQueryString(obj) {
  const params = new URLSearchParams();

  function addParams(prefix, value) {
    if (value !== null && typeof value === 'object') {
      for (const key in value) {
        if (value.hasOwnProperty(key)) {
          addParams(prefix ? `${prefix}[${key}]` : key, value[key]);
        }
      }
    } else {
      params.append(prefix, value);
    }
  }

  addParams('', obj);

  return params.toString();
}

function createDatePicker(selector) {
    return new AirDatepicker(selector, {
        locale: localeEn,
        dateFormat(date) {
            return date.toLocaleString("en-US", {
                year: "numeric",
                day: "2-digit",
                month: "2-digit",
            });
        },
        onRenderCell: ({ date, cellType }) => {
            const today = new Date();
            const response = {
                disabled: true,
                classes: "disabled-class",
                attrs: {
                    title: "Cell is disabled",
                },
            };

            if (cellType === "day") {
                const cellDate = new Date(date);
                cellDate.setHours(0, 0, 0, 0);

                if (cellDate < today) {
                    return response;
                }
            }
        },
        onSelect: ({ date, formattedDate, datepicker }) => {
          const event = new Event('input', {
            cancelable: true,
            bubbles: true,
          })

          datepicker.$el.dispatchEvent(event);
          datepicker.hide();
        },
    });
}

function makeQuill(editor) {
    {
        const image = editor.getAttribute('data-image') === "true" ? ["link", "image"] : ["link"];
        
        const quill = new Quill(editor, {
            theme: "snow",
            modules: {
                toolbar: {
                    container: [
                        ["bold", "italic", "underline"],
                        [{ size: ["small", false, "large"] }],
                        image,
                        [{ list: "ordered" }, { list: "bullet" }],
                        [{ align: ["", "center", "right", "justify"] }],
                        ["clean"],
                    ],
                    handlers: {
                        image: () => {
                            const input = document.createElement("input");
                            input.setAttribute("type", "file");
                            input.setAttribute("accept", "image/*");
                            input.click();

                            input.onchange = () => {
                                const formData = new FormData();
                                formData.append("image", input.files[0]);

                                axios
                                    .post("/api/data/upload-image", formData, {
                                        headers: {
                                            "Content-Type":
                                                "multipart/form-data",
                                        },
                                    })
                                    .then((response) => {
                                        const data = response.data;
                                        if (data.status === "error") {
                                            $.toast({
                                                text: data.message,
                                                icon: "error",
                                                heading: "Error",
                                                position: "top-right",
                                            });
                                        }

                                        const range = quill.getSelection();
                                        quill.insertEmbed(
                                            range.index,
                                            "image",
                                            data.path
                                        );
                                    })
                                    .catch((error) => {
                                        console.error(
                                            "Ошибка при загрузке",
                                            error
                                        );
                                    });
                            };
                        },
                    },
                },
                clipboard: {
                    // Сохраняем форматирование при вставке из Word
                    matchVisual: false,
                    // Включаем обработку HTML из Word
                    matchers: [],
                },
            },
            placeholder: editor.getAttribute("data-placeholder") ?? "",
        });
        
        // Сохраняем ссылку на Quill экземпляр для доступа из других мест
        editor.__quill = quill;

        // Улучшенный обработчик вставки из Word с сохранением форматирования
        // Обрабатываем ссылки
        quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
            const Delta = Quill.import('delta');
            const tagName = node.tagName ? node.tagName.toLowerCase() : '';
            
            // Обрабатываем ссылки - сохраняем href и текст
            if (tagName === 'a') {
                const href = node.getAttribute('href');
                if (href && delta.length() > 0) {
                    // Применяем ссылку ко всему содержимому
                    delta = delta.compose(new Delta().retain(delta.length(), { link: href }));
                }
            }
            
            return delta;
        });
        
        // Обрабатываем списки и их элементы
        quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
            const Delta = Quill.import('delta');
            const tagName = node.tagName ? node.tagName.toLowerCase() : '';
            
            // Обрабатываем элементы списка
            if (tagName === 'li') {
                const parent = node.parentNode;
                if (parent) {
                    const parentTag = parent.tagName ? parent.tagName.toLowerCase() : '';
                    if (parentTag === 'ul') {
                        // Маркированный список
                        if (delta.length() > 0) {
                            delta = delta.compose(new Delta().retain(delta.length(), { list: 'bullet' }));
                        }
                    } else if (parentTag === 'ol') {
                        // Нумерованный список
                        if (delta.length() > 0) {
                            delta = delta.compose(new Delta().retain(delta.length(), { list: 'ordered' }));
                        }
                    }
                }
            }
            
            return delta;
        });
        
        // Обрабатываем параграфы и div'ы - сохраняем выравнивание и размер
        quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
            const Delta = Quill.import('delta');
            const tagName = node.tagName ? node.tagName.toLowerCase() : '';
            
            if (tagName === 'p' || tagName === 'div') {
                const attrs = {};
                const style = node.getAttribute('style') || '';
                const computedStyle = window.getComputedStyle ? window.getComputedStyle(node) : null;
                
                // Обрабатываем выравнивание
                let textAlign = node.style?.textAlign || 
                               (computedStyle ? computedStyle.textAlign : null) ||
                               (style.match(/text-align:\s*([^;]+)/i)?.[1]?.trim());
                
                if (textAlign && ['left', 'center', 'right', 'justify'].includes(textAlign)) {
                    attrs.align = textAlign;
                }
                
                // Обрабатываем размер шрифта
                let fontSize = node.style?.fontSize || 
                              (computedStyle ? computedStyle.fontSize : null) ||
                              (style.match(/font-size:\s*([^;]+)/i)?.[1]?.trim());
                
                if (fontSize) {
                    const sizeValue = parseFloat(fontSize);
                    if (!isNaN(sizeValue)) {
                        if (sizeValue < 12) {
                            attrs.size = 'small';
                        } else if (sizeValue > 16) {
                            attrs.size = 'large';
                        }
                    }
                }
                
                // Применяем атрибуты к дельте только если есть контент
                if (Object.keys(attrs).length > 0 && delta.length() > 0) {
                    delta = delta.compose(new Delta().retain(delta.length(), attrs));
                }
            }
            
            return delta;
        });

        const id = editor.getAttribute("data-model");
        const wrap = editor.closest(".text-editor");
        const input = wrap?.querySelector(`#${id}`);

        // Функция для обновления значения input
        const updateInput = () => {
            if (id && input) {
                const content = quill.root.innerHTML;
                
                // Логируем для отладки
                console.log('Quill content length:', content.length);
                console.log('Quill content preview:', content.substring(0, 200));
                
                input.value = content;

                // Используем Livewire синхронизацию для обновления
                const event = new Event("input", {
                    bubbles: true,
                    cancelable: true,
                });

                input.dispatchEvent(event);
                
                // Также триггерим change событие для Livewire
                const changeEvent = new Event("change", {
                    bubbles: true,
                    cancelable: true,
                });
                input.dispatchEvent(changeEvent);
                
                // Для Livewire используем wire:model синхронизацию через событие
                // Livewire автоматически отслеживает изменения в input с wire:model
                if (window.Livewire && input.hasAttribute('wire:model')) {
                    // Дополнительно триггерим событие для Livewire
                    const livewireEvent = new CustomEvent('input', {
                        bubbles: true,
                        cancelable: true,
                        detail: { value: content }
                    });
                    input.dispatchEvent(livewireEvent);
                }
            }

            const counter = wrap?.querySelector(".text-counter");
            if (counter) {
                counter.innerHTML = quill.getLength() - 1;
            }
        };

        // Простая функция для загрузки контента в Quill
        const loadContentIntoQuill = () => {
          let content = input?.value || '';
          
          if (!content) {
            // Если контента нет, проверяем еще раз через некоторое время (для Livewire)
            setTimeout(() => loadContentIntoQuill(), 500);
            return;
          }
          
          // Просто загружаем контент напрямую через innerHTML
          quill.root.innerHTML = content;
          
          // Применяем стили к спискам
          setTimeout(() => {
              const editorElement = quill.root;
              const allLists = editorElement.querySelectorAll('ul, ol');
              
              allLists.forEach(list => {
                  if (list.tagName === 'UL') {
                      list.style.listStyleType = 'disc';
                      list.style.listStyle = 'disc outside';
                  } else if (list.tagName === 'OL') {
                      list.style.listStyleType = 'decimal';
                      list.style.listStyle = 'decimal outside';
                  }
                  list.style.listStylePosition = 'outside';
                  list.style.paddingLeft = '30px';
                  list.style.margin = '15px 0';
              });
              
              const listItems = editorElement.querySelectorAll('li');
              listItems.forEach(li => {
                  li.style.display = 'list-item';
                  li.style.listStylePosition = 'outside';
                  li.style.margin = '8px 0';
                  li.style.paddingLeft = '5px';
              });
              
              // Обновляем input
              if (input) {
                  input.value = editorElement.innerHTML;
              }
          }, 100);
        };
        
        // Загружаем контент после инициализации Quill
        setTimeout(loadContentIntoQuill, 300);

        // Обработчик изменений текста
        quill.on("text-change", updateInput);
        
        // Обработчик изменений редактора (включая вставку)
        quill.on("editor-change", (eventName, ...args) => {
            if (eventName === 'text-change' || eventName === 'selection-change') {
                // Небольшая задержка для обработки вставки
                setTimeout(updateInput, 10);
            }
        });
        
        // Дополнительный обработчик для вставки через paste - улучшенная обработка из Word
        editor.addEventListener('paste', function(e) {
            // Получаем данные из буфера обмена
            const clipboardData = e.clipboardData || window.clipboardData;
            const htmlData = clipboardData.getData('text/html');
            const textData = clipboardData.getData('text/plain');
            
            // Если есть HTML данные (из Word), обрабатываем их
            if (htmlData) {
                // Создаем временный элемент для парсинга HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = htmlData;
                
                // Обрабатываем ссылки - убеждаемся, что они сохраняются
                const links = tempDiv.querySelectorAll('a');
                links.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && !href.startsWith('#')) {
                        // Сохраняем ссылку
                        link.setAttribute('href', href);
                    }
                });
                
                // Обрабатываем списки - убеждаемся, что структура сохраняется
                const lists = tempDiv.querySelectorAll('ul, ol');
                lists.forEach(list => {
                    // Сохраняем структуру списка
                    list.setAttribute('data-list-type', list.tagName.toLowerCase());
                });
            }
            
            // Позволяем Quill обработать вставку стандартным способом
            // Затем принудительно обновляем input
            
            setTimeout(() => {
                updateInput();
                
                // Применяем стили к спискам после вставки
                setTimeout(() => {
                    const editorElement = quill.root;
                    const allLists = editorElement.querySelectorAll('ul, ol');
                    
                    allLists.forEach(list => {
                        if (list.tagName === 'UL') {
                            list.style.listStyleType = 'disc';
                            list.style.listStyle = 'disc outside';
                        } else if (list.tagName === 'OL') {
                            list.style.listStyleType = 'decimal';
                            list.style.listStyle = 'decimal outside';
                        }
                        list.style.listStylePosition = 'outside';
                        list.style.paddingLeft = '30px';
                        list.style.margin = '15px 0';
                    });
                    
                    const listItems = editorElement.querySelectorAll('li');
                    listItems.forEach(li => {
                        li.style.display = 'list-item';
                        li.style.listStylePosition = 'outside';
                        li.style.margin = '8px 0';
                        li.style.paddingLeft = '5px';
                    });
                    
                    // Обновляем input с правильным HTML
                    if (input) {
                        input.value = editorElement.innerHTML;
                    }
                }, 50);
                
                // Дополнительное обновление для Livewire с небольшой задержкой
                setTimeout(() => {
                    if (input) {
                        // Триггерим все необходимые события для Livewire
                        input.dispatchEvent(new Event('input', { bubbles: true, cancelable: true }));
                        input.dispatchEvent(new Event('change', { bubbles: true, cancelable: true }));
                        input.dispatchEvent(new Event('blur', { bubbles: true, cancelable: true }));
                    }
                }, 150);
            }, 50);
        }, false);

        return quill;
    }
}
const builded_editors = [];
window.addEventListener("DOMContentLoaded", function () {
    const editors = document.querySelectorAll(".quill-editor");
    editors.forEach((editor) => {
      if (!builded_editors.includes(editor)) {
        makeQuill(editor);
        builded_editors.push(editor);
      }
    });

    Livewire.hook("morphed", () => {
        const editors = document.querySelectorAll(".quill-editor");
        editors.forEach((editor) => {
          if (!builded_editors.includes(editor)) {
            makeQuill(editor);
            builded_editors.push(editor);
          } else {
            // Если редактор уже создан, просто перезагружаем контент
            const quillInstance = editor.__quill;
            if (quillInstance) {
              const id = editor.getAttribute("data-model");
              const wrap = editor.closest(".text-editor");
              const input = wrap?.querySelector(`#${id}`);
              if (input && input.value) {
                setTimeout(() => {
                  quillInstance.root.innerHTML = input.value;
                  
                  // Применяем стили к спискам
                  setTimeout(() => {
                    const allLists = quillInstance.root.querySelectorAll('ul, ol');
                    allLists.forEach(list => {
                      if (list.tagName === 'UL') {
                        list.style.listStyleType = 'disc';
                        list.style.listStyle = 'disc outside';
                      } else if (list.tagName === 'OL') {
                        list.style.listStyleType = 'decimal';
                        list.style.listStyle = 'decimal outside';
                      }
                      list.style.listStylePosition = 'outside';
                      list.style.paddingLeft = '30px';
                      list.style.margin = '15px 0';
                    });
                    
                    const listItems = quillInstance.root.querySelectorAll('li');
                    listItems.forEach(li => {
                      li.style.display = 'list-item';
                      li.style.listStylePosition = 'outside';
                      li.style.margin = '8px 0';
                      li.style.paddingLeft = '5px';
                    });
                  }, 100);
                }, 200);
              }
            }
          }
        });
    });
});
