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
// Expose english locale for pages that initialize datepickers inline.
window.localeEn = localeEn;

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

function createBirthdayDatePicker(selector) {
    const input = document.querySelector(selector);
    if (!input) return null;

    // Если значение уже есть в формате YYYY-MM-DD, конвертируем его для отображения
    const currentValue = input.value;
    let initialDate = null;
    if (currentValue) {
        // Проверяем формат YYYY-MM-DD
        const dateMatch = currentValue.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (dateMatch) {
            initialDate = new Date(parseInt(dateMatch[1]), parseInt(dateMatch[2]) - 1, parseInt(dateMatch[3]));
        } else {
            // Пытаемся распарсить другие форматы (MM/DD/YYYY)
            const parts = currentValue.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
            if (parts) {
                initialDate = new Date(parseInt(parts[3]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            } else {
                const parsed = new Date(currentValue);
                if (!isNaN(parsed.getTime())) {
                    initialDate = parsed;
                }
            }
        }
    }

    // Создаем скрытое поле для правильного формата даты
    let hiddenInput = input.parentElement.querySelector('input[name="birthday"][type="hidden"]');
    if (!hiddenInput && currentValue) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'birthday';
        // Если значение в формате YYYY-MM-DD, сохраняем его
        if (currentValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
            hiddenInput.value = currentValue;
        } else if (initialDate) {
            const year = initialDate.getFullYear();
            const month = String(initialDate.getMonth() + 1).padStart(2, '0');
            const day = String(initialDate.getDate()).padStart(2, '0');
            hiddenInput.value = `${year}-${month}-${day}`;
        }
        input.parentElement.appendChild(hiddenInput);
    }

    const picker = new AirDatepicker(selector, {
        locale: localeEn,
        dateFormat(date) {
            return date.toLocaleString("en-US", {
                year: "numeric",
                day: "2-digit",
                month: "2-digit",
            });
        },
        selectedDates: initialDate ? [initialDate] : [],
        maxDate: new Date(), // Нельзя выбрать будущую дату
        autoClose: true,
        isMobile: false,
        onRenderCell: ({ date, cellType }) => {
            const today = new Date();
            today.setHours(23, 59, 59, 999);
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

                // Отключаем будущие даты
                if (cellDate > today) {
                    return response;
                }
            }
        },
        onSelect: ({ date, formattedDate, datepicker }) => {
          // Форматируем дату для отображения в формате MM/DD/YYYY
          const displayFormat = date.toLocaleString("en-US", {
              year: "numeric",
              day: "2-digit",
              month: "2-digit",
          });
          
          // Устанавливаем отображаемое значение
          datepicker.$el.value = displayFormat;
          
          // Форматируем дату для скрытого поля
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const day = String(date.getDate()).padStart(2, '0');
          const hiddenValue = `${year}-${month}-${day}`;
          
          // Сохраняем ссылки на элементы для использования после закрытия календаря
          const inputElement = datepicker.$el;
          const parentElement = inputElement.parentElement;
          
          // Закрываем календарь сначала
          datepicker.hide();
          
          // Обновляем DOM после того, как календарь закрылся
          setTimeout(function() {
              // Находим или создаем скрытое поле
              let hiddenInput = parentElement.querySelector('input[name="birthday"][type="hidden"]');
              if (!hiddenInput) {
                  hiddenInput = document.createElement('input');
                  hiddenInput.type = 'hidden';
                  hiddenInput.name = 'birthday';
                  parentElement.appendChild(hiddenInput);
              }
              
              // Устанавливаем значение скрытого поля
              hiddenInput.value = hiddenValue;
              
              // Удаляем name из видимого input только если скрытое поле создано
              if (hiddenInput && hiddenInput.parentElement) {
                  inputElement.removeAttribute('name');
              }
              
              // Триггерим событие input
              const event = new Event('input', {
                cancelable: true,
                bubbles: true,
              });
              inputElement.dispatchEvent(event);
          }, 100);
        },
    });

    return picker;
}

// Экспортируем функции в window после их объявления
window.createDatePicker = createDatePicker;
window.createBirthdayDatePicker = createBirthdayDatePicker;
window.objectToQueryString = objectToQueryString;

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
                    // Включаем обработку HTML из Word - сохраняем все форматирование
                    matchers: [],
                },
            },
            placeholder: editor.getAttribute("data-placeholder") ?? "",
        });
        
        // Сохраняем ссылку на Quill экземпляр для доступа из других мест
        editor.__quill = quill;

        // Импортируем Delta один раз для использования во всех matchers
            const Delta = Quill.import('delta');

        // Улучшенный обработчик вставки из Word с сохранением форматирования 1:1
        
        // Обрабатываем форматирование текста (жирный, курсив, подчеркивание)
        quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
            const tagName = node.tagName ? node.tagName.toLowerCase() : '';
            const attrs = {};
            
            // Жирный текст (strong, b)
            if (tagName === 'strong' || tagName === 'b') {
                attrs.bold = true;
            }
            
            // Курсив (em, i)
            if (tagName === 'em' || tagName === 'i') {
                attrs.italic = true;
            }
            
            // Подчеркивание (u)
            if (tagName === 'u') {
                attrs.underline = true;
            }
            
            // Заголовки - преобразуем в размер
            if (tagName.match(/^h[1-6]$/)) {
                const level = parseInt(tagName.charAt(1));
                if (level <= 2) {
                    attrs.size = 'large';
                } else if (level >= 5) {
                    attrs.size = 'small';
                }
                attrs.bold = true; // Заголовки обычно жирные
            }
            
            // Применяем атрибуты
            if (Object.keys(attrs).length > 0 && delta.length() > 0) {
                delta = delta.compose(new Delta().retain(delta.length(), attrs));
            }
            
            return delta;
        });
        
        // Обрабатываем стили из атрибута style (Word часто использует inline стили)
        quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
            const style = node.getAttribute('style') || '';
            const attrs = {};
            
            if (style) {
                // Жирный через font-weight
                if (style.match(/font-weight:\s*(bold|700|800|900)/i)) {
                    attrs.bold = true;
                }
                
                // Курсив через font-style
                if (style.match(/font-style:\s*italic/i)) {
                    attrs.italic = true;
                }
                
                // Подчеркивание через text-decoration
                if (style.match(/text-decoration:\s*underline/i)) {
                    attrs.underline = true;
                }
                
                // Размер шрифта
                const fontSizeMatch = style.match(/font-size:\s*([\d.]+)px/i);
                if (fontSizeMatch) {
                    const size = parseFloat(fontSizeMatch[1]);
                    if (size < 12) {
                        attrs.size = 'small';
                    } else if (size > 16) {
                        attrs.size = 'large';
                    }
                }
                
                // Выравнивание
                const textAlignMatch = style.match(/text-align:\s*([^;]+)/i);
                if (textAlignMatch) {
                    const align = textAlignMatch[1].trim();
                    if (['left', 'center', 'right', 'justify'].includes(align)) {
                        attrs.align = align;
                    }
                }
            }
            
            // Применяем атрибуты
            if (Object.keys(attrs).length > 0 && delta.length() > 0) {
                delta = delta.compose(new Delta().retain(delta.length(), attrs));
            }
            
            return delta;
        });
            
            // Обрабатываем ссылки - сохраняем href и текст
        quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
            const tagName = node.tagName ? node.tagName.toLowerCase() : '';
            
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
        
        // Обрабатываем вложенные элементы форматирования (например, strong внутри em)
        // Quill автоматически обрабатывает вложенные элементы, но нужно убедиться, что они применяются правильно
        quill.clipboard.addMatcher(Node.TEXT_NODE, (node, delta) => {
            // Обрабатываем текстовые узлы для сохранения форматирования родительских элементов
            let parent = node.parentNode;
            const attrs = {};
            
            while (parent && parent.nodeType === Node.ELEMENT_NODE) {
                const tagName = parent.tagName ? parent.tagName.toLowerCase() : '';
                const style = parent.getAttribute('style') || '';
                
                // Проверяем форматирование родительских элементов
                if (tagName === 'strong' || tagName === 'b' || style.match(/font-weight:\s*(bold|700|800|900)/i)) {
                    attrs.bold = true;
                }
                if (tagName === 'em' || tagName === 'i' || style.match(/font-style:\s*italic/i)) {
                    attrs.italic = true;
                }
                if (tagName === 'u' || style.match(/text-decoration:\s*underline/i)) {
                    attrs.underline = true;
                }
                
                parent = parent.parentNode;
            }
            
            // Применяем атрибуты к дельте
            if (Object.keys(attrs).length > 0 && delta.length() > 0) {
                delta = delta.compose(new Delta().retain(delta.length(), attrs));
            }
            
            return delta;
        });

        const id = editor.getAttribute("data-model");
        const wrap = editor.closest(".text-editor");
        const input = wrap?.querySelector(`#${id}`);
        
        
        // Функция для обновления счетчика символов
        const updateCharCounter = () => {
            try {
                const text = quill.getText();
                const charCount = Math.max(0, text.length - 1);
                
                // Ищем счетчик по data-атрибуту с data-model
                const counterElement = document.querySelector(`span.char-count-display[data-model="${id}"]`);
                
                if (counterElement) {
                    counterElement.textContent = charCount;
                } else {
                    // Альтернативный поиск в контейнере text-editor
                    if (wrap) {
                        const counterByData = wrap.querySelector('[data-char-counter="true"] span:first-child');
                        if (counterByData) {
                            counterByData.textContent = charCount;
                        } else {
                            const counterByClass = wrap.querySelector('.char-count-display');
                            if (counterByClass) {
                                counterByClass.textContent = charCount;
                            }
                        }
                    }
                }
            } catch (e) {
                console.log('Char counter update error:', e);
            }
        };

        // Флаг для предотвращения загрузки контента после вставки
        let isPasting = false;
        let pasteTimeout = null;

        // Функция для обновления значения input
        // Флаг для предотвращения циклов обновления
        let isUpdating = false;
        let lastUpdateTime = 0;
        let lastUpdateContent = '';
        
        const updateInput = () => {
            // Защита от циклов: не обновляем чаще чем раз в 200мс
            const now = Date.now();
            const content = quill.root.innerHTML;
            
            // Если контент не изменился и прошло меньше 200мс, не обновляем
            if (isUpdating || (now - lastUpdateTime < 200 && content === lastUpdateContent)) {
                return;
            }
            
            isUpdating = true;
            lastUpdateTime = now;
            lastUpdateContent = content;
            
            if (id && input) {
                // Получаем HTML напрямую из DOM
                const htmlString = quill.root.innerHTML;
                
                // Устанавливаем значение просто и без лишних проверок
                if (input.value !== htmlString) {
                    input.value = htmlString;
                    
                    // Триггерим событие для Livewire (только если значение изменилось)
                    const inputEvent = new Event("input", {
                        bubbles: true,
                        cancelable: true,
                    });
                    input.dispatchEvent(inputEvent);
                }
                
            }
            
            // Снимаем флаг после небольшой задержки
            setTimeout(() => {
                isUpdating = false;
            }, 300); // Увеличиваем задержку для предотвращения конфликтов

            const counter = wrap?.querySelector(".text-counter");
            if (counter) {
                counter.innerHTML = quill.getLength() - 1;
            }
            
            // Ограничение длины текста до максимума (если указан data-max)
            const maxLength = parseInt(editor.getAttribute('data-max')) || null;
            if (maxLength) {
                const text = quill.getText();
                const currentLength = text.length - 1; // -1 для учета финального символа новой строки
                if (currentLength > maxLength) {
                    const delta = quill.getContents();
                    const newDelta = delta.slice(0, maxLength);
                    quill.setContents(newDelta);
                    quill.setSelection(maxLength);
                }
            }
            
            // Обновление счетчика символов для Quill редактора
            updateCharCounter();
        };

        // Флаг для отслеживания, был ли контент загружен при инициализации
        let initialContentLoaded = false;

        // Простая функция для загрузки контента в Quill (только один раз при инициализации)
        const loadContentIntoQuill = () => {
          // КРИТИЧЕСКИ ВАЖНО: НИ ПРИ КАКИХ УСЛОВИЯХ не перезаписываем контент, если пользователь уже работал с редактором
          
          // Не загружаем контент, если идет процесс вставки
          if (isPasting) {
            return;
          }
          
          let content = input?.value || '';
          
          // Проверяем текущий контент в редакторе
          const currentContent = quill.root.innerHTML;
          const hasUserContent = currentContent && currentContent.trim() !== '' && currentContent !== '<p><br></p>';
          
          // ВАЖНО: Если в редакторе уже есть контент (даже минимальный),
          // НИ ПРИ КАКИХ УСЛОВИЯХ не перезаписываем его
          // Это защищает от потери текста при любых обновлениях Livewire
          if (hasUserContent) {
            // Контент в редакторе уже есть - НИКОГДА не перезаписываем
            // Синхронизируем обратно в input, чтобы сохранить актуальное значение
            if (input && input.value !== currentContent) {
              input.value = currentContent;
              // Триггерим событие для Livewire
              const inputEvent = new Event("input", { bubbles: true, cancelable: true });
              input.dispatchEvent(inputEvent);
            }
            return; // НЕ ПЕРЕЗАПИСЫВАЕМ контент пользователя
          }
          
          // Загружаем контент ТОЛЬКО если редактор полностью пустой (нет контента пользователя)
          // И только при первой инициализации
          if (!initialContentLoaded && content) {
          quill.root.innerHTML = content;
            initialContentLoaded = true;
          }
          
          // Применяем стили к спискам только один раз при загрузке
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
          
          // Обновляем счетчик только один раз
          updateCharCounter();
        };
        
        // Загружаем контент только один раз после инициализации Quill
        // Используем флаг, чтобы не загружать контент, если идет процесс вставки
        let contentLoaded = false;
        const initLoadContent = () => {
            // Загружаем контент ТОЛЬКО один раз при первой инициализации
            if (!contentLoaded && !isPasting) {
                loadContentIntoQuill();
                contentLoaded = true;
            }
            // Больше НЕ загружаем контент автоматически после этого
        };
        setTimeout(initLoadContent, 100);
        
        // КРИТИЧЕСКАЯ ЗАЩИТА: Сохраняем контент перед ЛЮБЫМ обновлением Livewire
        // и восстанавливаем его, если он был потерян
        if (window.Livewire) {
            Livewire.hook('morph', ({ el, component, skip }) => {
                // Сохраняем контент перед ЛЮБЫМ обновлением
                const currentContent = quill.root.innerHTML;
                if (currentContent && currentContent.trim() !== '' && currentContent !== '<p><br></p>') {
                    // Сохраняем контент во временное хранилище с timestamp
                    sessionStorage.setItem(`quill-content-${id}`, currentContent);
                    sessionStorage.setItem(`quill-content-time-${id}`, Date.now().toString());
                }
            });
            
            // Восстанавливаем контент после обновления, если он был потерян
            Livewire.hook('morphed', ({ component }) => {
                const savedContent = sessionStorage.getItem(`quill-content-${id}`);
                if (savedContent) {
                    const currentContent = quill.root.innerHTML;
                    // Если редактор стал пустым или содержит только пустой параграф,
                    // восстанавливаем сохраненный контент
                    if (!currentContent || currentContent.trim() === '' || currentContent === '<p><br></p>') {
                        quill.root.innerHTML = savedContent;
                        // Обновляем input
                        if (input) {
                            input.value = savedContent;
                            // Триггерим событие для Livewire
                            const inputEvent = new Event("input", { bubbles: true, cancelable: true });
                            input.dispatchEvent(inputEvent);
                        }
                    }
                    // Удаляем сохраненный контент после восстановления
                    sessionStorage.removeItem(`quill-content-${id}`);
                    sessionStorage.removeItem(`quill-content-time-${id}`);
                }
            });
        }
        
        // Синхронизируем значение из Quill в input перед отправкой формы
        // Это гарантирует, что последние изменения будут отправлены на сервер
        const syncBeforeSubmit = () => {
            if (quill && input) {
                const currentContent = quill.root.innerHTML;
                if (currentContent && currentContent.trim() !== '' && currentContent !== '<p><br></p>') {
                    // Отменяем любые ожидающие обновления
                    if (textChangeTimeout) {
                        clearTimeout(textChangeTimeout);
                        textChangeTimeout = null;
                    }
                    
                    // Синхронизируем немедленно перед отправкой
                    isUpdating = true;
                    
                    // Обновляем input значение
                    input.value = currentContent;
                    
                    // Триггерим событие для Livewire (синхронно)
                    const inputEvent = new Event("input", {
                        bubbles: true,
                        cancelable: true,
                    });
                    input.dispatchEvent(inputEvent);
                    
                    // Также триггерим change событие для надежности
                    const changeEvent = new Event("change", {
                        bubbles: true,
                        cancelable: true,
                    });
                    input.dispatchEvent(changeEvent);
                    
                    // КРИТИЧЕСКИ ВАЖНО: Обновляем через Livewire напрямую, если доступно
                    if (window.Livewire && input.hasAttribute('wire:model')) {
                        const wireModel = input.getAttribute('wire:model');
                        if (wireModel) {
                            // Находим компонент Livewire
                            const wireId = input.closest('[wire\\:id]')?.getAttribute('wire:id');
                            if (wireId) {
                                const component = Livewire.find(wireId);
                                if (component) {
                                    try {
                                        // Устанавливаем значение напрямую в компонент (синхронно)
                                        if (component.set) {
                                            component.set(wireModel, currentContent);
                                        }
                                        // Также обновляем через $wire, если доступно
                                        if (component.$wire && component.$wire.set) {
                                            component.$wire.set(wireModel, currentContent);
                                        }
                                        // Также обновляем через $wire.$get, если доступно
                                        if (component.$wire && component.$wire.$get) {
                                            const currentValue = component.$wire.$get(wireModel);
                                            if (currentValue !== currentContent) {
                                                component.$wire.$set(wireModel, currentContent);
                                            }
                                        }
                                        // КРИТИЧЕСКИ ВАЖНО: Также обновляем напрямую в snapshot компонента
                                        if (component.$wire && component.$wire._x_model) {
                                            // Обновляем через Alpine.js модель, если доступно
                                            try {
                                                component.$wire._x_model.set(wireModel, currentContent);
                                            } catch (e) {
                                                // Игнорируем ошибки
                                            }
                                        }
                                    } catch (e) {
                                        console.log('Error setting Livewire value:', e);
                                    }
                                }
                            }
                        }
                    }
                    
                    // Принудительно обновляем значение в DOM для надежности
                    if (input.value !== currentContent) {
                        input.value = currentContent;
                    }
                    
                    // Снимаем флаг после небольшой задержки
                    setTimeout(() => {
                        isUpdating = false;
                    }, 100);
                    
                    // Возвращаем true для индикации успешной синхронизации
                    return true;
                } else {
                }
            } else {
            }
            return false;
        };
        
        // Перехватываем отправку формы и синхронизируем значение
        const form = editor.closest('form');
        if (form) {
            form.addEventListener('submit', (e) => {
                syncBeforeSubmit();
            });
        }
        
        // КРИТИЧЕСКИ ВАЖНО: Перехватываем Livewire запросы перед отправкой
        // Используем несколько hooks для надежности
        if (window.Livewire) {
            
            // Hook 1: Перехватываем перед отправкой сообщения
            Livewire.hook('message', ({ component, message, respond, succeed, fail }) => {
                // Проверяем, содержит ли запрос метод submit или draft
                // Проверяем несколько возможных структур запроса
                const update = message.updateQueue || [];
                const effects = message.effects || {};
                const calls = effects.calls || [];
                
                // Проверяем в updateQueue
                const hasSubmitOrDraftInQueue = update.some(item => 
                    item.type === 'callMethod' && 
                    item.payload && 
                    (item.payload.method === 'submit' || item.payload.method === 'draft')
                );
                
                // Проверяем в effects.calls
                const hasSubmitOrDraftInCalls = calls.some(call => 
                    call && (call.method === 'submit' || call.method === 'draft')
                );
                
                // Также проверяем в самом message, если есть fingerprint
                let hasSubmitOrDraftInFingerprint = false;
                if (message.fingerprint && message.fingerprint.memo) {
                    const memo = message.fingerprint.memo;
                    // Проверяем, есть ли вызов метода в memo
                    if (memo.method && (memo.method === 'submit' || memo.method === 'draft')) {
                        hasSubmitOrDraftInFingerprint = true;
                    }
                }
                
                const hasSubmitOrDraft = hasSubmitOrDraftInQueue || hasSubmitOrDraftInCalls || hasSubmitOrDraftInFingerprint;
                
                if (hasSubmitOrDraft) {
                    // КРИТИЧЕСКИ ВАЖНО: Синхронизируем значение ПЕРЕД отправкой запроса
                    const synced = syncBeforeSubmit();
                    
                    // КРИТИЧЕСКИ ВАЖНО: Обновляем значение в payload запроса ПЕРЕД отправкой
                    // Это гарантирует, что актуальное значение из Quill будет отправлено на сервер
                    if (quill && input) {
                        const currentContent = quill.root.innerHTML;
                        
                        // Напрямую обновляем значение в payload запроса
                        // Ищем все обновления свойств и обновляем fields.text
                        let updatedCount = 0;
                        update.forEach(item => {
                            if (item.type === 'syncInput' && item.payload && item.payload.name === 'fields.text') {
                                const oldValue = item.payload.value;
                                // КРИТИЧЕСКИ ВАЖНО: Обновляем значение из Quill редактора
                                item.payload.value = currentContent;
                                updatedCount++;
                            }
                        });
                        
                        // Также обновляем в самом сообщении, если есть
                        if (message.fingerprint && message.fingerprint.memo) {
                            const memo = message.fingerprint.memo;
                            if (memo.data && memo.data.fields && memo.data.fields.text !== undefined) {
                                const oldMemoValue = memo.data.fields.text;
                                // КРИТИЧЕСКИ ВАЖНО: Обновляем значение в memo
                                memo.data.fields.text = currentContent;
                            }
                        }
                        
                        // Если не нашли syncInput для fields.text, добавляем его вручную
                        if (updatedCount === 0 && currentContent && currentContent.trim() !== '' && currentContent !== '<p><br></p>') {
                            // Добавляем syncInput в updateQueue
                            update.push({
                                type: 'syncInput',
                                payload: {
                                    name: 'fields.text',
                                    value: currentContent
                                }
                            });
                        }
                    }
                    
                    // Ждем, чтобы Livewire успел получить обновленное значение
                    // Используем микро-задержку для синхронизации
                    return new Promise((resolve) => {
                        // Даем время на обновление input и Livewire
                        // Используем несколько кадров для надежности
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                setTimeout(() => {
                                    resolve(respond());
                                }, 100);
                            });
                        });
                    });
                }
                
                return respond();
            });
        }
        
        // КРИТИЧЕСКИ ВАЖНО: Перехватываем клики по кнопкам сохранения
        // Ищем кнопку по тексту и по всем элементам вверх по DOM дереву
        document.addEventListener('click', (e) => {
            const target = e.target;
            let button = null;
            let wireClickValue = null;
            
            // Ищем кнопку вверх по DOM дереву (до 10 уровней)
            let element = target;
            for (let i = 0; i < 10 && element; i++) {
                // Проверяем все возможные варианты атрибутов wire:click
                wireClickValue = element.getAttribute('wire:click') || 
                                element.getAttribute('wire-click') ||
                                element.getAttribute('data-wire-click');
                
                if (wireClickValue) {
                    button = element;
                    break;
                }
                
                // Также проверяем по тексту кнопки
                const text = element.textContent?.trim() || '';
                if ((text.includes('Save as Draft') || text.includes('Save & Continue') || text.includes('Publish')) && 
                    (element.tagName === 'BUTTON' || element.closest('button'))) {
                    button = element.tagName === 'BUTTON' ? element : element.closest('button');
                    // Ищем wire:click в найденной кнопке
                    if (button) {
                        wireClickValue = button.getAttribute('wire:click') || 
                                        button.getAttribute('wire-click') ||
                                        button.getAttribute('data-wire-click');
                    }
                    break;
                }
                
                element = element.parentElement;
            }
            
            // Проверяем, содержит ли wire:click метод submit или draft
            const isSubmitOrDraft = wireClickValue && (
                wireClickValue === 'submit' || 
                wireClickValue === 'draft' ||
                wireClickValue.includes('submit') || 
                wireClickValue.includes('draft')
            );
            
            
            if (isSubmitOrDraft) {
                // КРИТИЧЕСКИ ВАЖНО: Синхронизируем ПЕРЕД отправкой запроса
                syncBeforeSubmit();
                
                // Также обновляем через Livewire напрямую, если доступно
                if (window.Livewire && input && input.hasAttribute('wire:model')) {
                    const wireModel = input.getAttribute('wire:model');
                    if (wireModel) {
                        const wireId = input.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (wireId) {
                            const component = Livewire.find(wireId);
                            if (component && quill) {
                                const currentContent = quill.root.innerHTML;
                                try {
                                    if (component.$wire && component.$wire.set) {
                                        component.$wire.set(wireModel, currentContent);
                                    }
                                } catch (e) {
                                    // Игнорируем ошибки
                                }
                            }
                        }
                    }
                }
                
            }
        }, true); // Используем capture phase для раннего перехвата ДО Livewire
        
        // КРИТИЧЕСКИ ВАЖНО: Перехватываем все Livewire запросы через fetch/XHR
        // Это более надежный способ перехвата, чем через hooks
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            const url = args[0];
            // Проверяем, является ли это запрос к Livewire
            if (typeof url === 'string' && (url.includes('/livewire/') || url.includes('/livewire/message'))) {
                // Синхронизируем значение перед отправкой запроса
                if (quill && input) {
                    const currentContent = quill.root.innerHTML;
                    if (currentContent && currentContent.trim() !== '' && currentContent !== '<p><br></p>') {
                        syncBeforeSubmit();
                    }
                }
            }
            return originalFetch.apply(this, args);
        };
        
        // Обновляем счетчик при каждом изменении текста
        quill.on('text-change', () => {
            updateCharCounter();
        });

        // Обработчик изменений текста - используем debounce для предотвращения циклов
        let textChangeTimeout = null;
        quill.on("text-change", (delta, oldDelta, source) => {
            // Игнорируем изменения от программного обновления (source === 'api')
            if (source === 'api' || isUpdating) {
                return;
            }
            
            // Отменяем предыдущий таймер
            if (textChangeTimeout) {
                clearTimeout(textChangeTimeout);
            }
            
            // Обновляем с задержкой, чтобы избежать множественных вызовов
            textChangeTimeout = setTimeout(() => {
                if (!isUpdating) {
                    updateInput();
                }
            }, 500); // Увеличиваем задержку для стабильности
        });
        
        // Отслеживаем потерю фокуса
        quill.on('selection-change', (range, oldRange, source) => {
            // Selection change handler
        });
        
        // Отслеживаем потерю фокуса на редакторе
        editor.addEventListener('blur', () => {
            // Blur handler
        });
        
        // Отслеживаем получение фокуса
        editor.addEventListener('focus', () => {
            // Focus handler
        });
        
        // Обработчик изменений редактора - отключаем для предотвращения циклов
        // quill.on("editor-change", (eventName, ...args) => {
        //     // Отключено для предотвращения циклов при вставке
        // });
        
        // Обработчик для вставки через paste - сохраняем все стили 1:1 из Word
        editor.addEventListener('paste', function(e) {
            // Устанавливаем флаг вставки
            isPasting = true;
            
            // Очищаем предыдущий таймер, если есть
            if (pasteTimeout) {
                clearTimeout(pasteTimeout);
            }
            
            // Получаем данные из буфера обмена
            const clipboardData = e.clipboardData || window.clipboardData;
            const htmlData = clipboardData.getData('text/html');
            const textData = clipboardData.getData('text/plain');
            // Если есть HTML данные (из Word), обрабатываем их с сохранением всех стилей
            if (htmlData) {
                e.preventDefault(); // Отменяем стандартную обработку
                
                // Создаем временный элемент для парсинга HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = htmlData;
                
                // Функция для очистки небезопасных элементов, но сохранения всех стилей
                const cleanAndPreserveStyles = (element) => {
                    // Разрешенные теги
                    const allowedTags = ['p', 'div', 'span', 'strong', 'b', 'em', 'i', 'u', 'a', 
                                       'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'br'];
                    
                    // Разрешенные атрибуты
                    const allowedAttributes = ['href', 'style', 'class'];
                    
                    // Обрабатываем все элементы
                    const allElements = element.querySelectorAll('*');
                    allElements.forEach(el => {
                        const tagName = el.tagName.toLowerCase();
                        
                        // Удаляем неразрешенные теги, но сохраняем их содержимое
                        if (!allowedTags.includes(tagName)) {
                            const parent = el.parentNode;
                            while (el.firstChild) {
                                parent.insertBefore(el.firstChild, el);
                            }
                            parent.removeChild(el);
                            return;
                        }
                        
                        // Очищаем неразрешенные атрибуты
                        Array.from(el.attributes).forEach(attr => {
                            if (!allowedAttributes.includes(attr.name.toLowerCase())) {
                                el.removeAttribute(attr.name);
                            }
                        });
                        
                        // Сохраняем все стили из атрибута style - очищаем только опасные
                        const style = el.getAttribute('style') || '';
                        if (style) {
                            // Очищаем только опасные стили (javascript:, expression и т.д.)
                            // Но сохраняем ВСЕ остальные стили, включая отступы и интервалы
                            let safeStyle = style
                                .replace(/javascript:/gi, '')
                                .replace(/expression\(/gi, '')
                                .replace(/on\w+\s*=/gi, '')
                                .replace(/url\s*\(\s*['"]?\s*javascript:/gi, 'url('); // Очищаем javascript в url()
                            
                            // Нормализуем line-height: ограничиваем разумными значениями (1.2-1.6)
                            safeStyle = safeStyle.replace(/line-height:\s*([\d.]+)(pt|px|em|rem|%)?/gi, (match, value, unit) => {
                                const numValue = parseFloat(value);
                                if (!isNaN(numValue)) {
                                    if (numValue > 1.6) {
                                        return 'line-height: 1.6'; // Максимум 1.6
                                    } else if (numValue < 1.2) {
                                        return 'line-height: 1.2'; // Минимум 1.2
                                    }
                                    // Оставляем как есть, если в пределах 1.2-1.6, но убираем единицы измерения для числовых значений
                                    if (!unit || unit === '') {
                                        return `line-height: ${numValue}`;
                                    }
                                }
                                return match; // Оставляем как есть, если не удалось распарсить
                            });
                            
                            el.setAttribute('style', safeStyle);
                        }
                        
                        // Для элементов без inline стилей сохраняем computed стили
                        // Это важно для элементов, у которых стили заданы через классы в Word
                        if (!el.getAttribute('style') && window.getComputedStyle) {
                            const computed = window.getComputedStyle(el);
                            const importantStyles = [];
                            
                            // Сохраняем ВСЕ важные стили: отступы, интервалы, выравнивание, размеры
                            const styleProps = [
                                'margin', 'margin-top', 'margin-bottom', 'margin-left', 'margin-right',
                                'padding', 'padding-top', 'padding-bottom', 'padding-left', 'padding-right',
                                'line-height', 'text-indent', 'text-align', 'font-size', 'font-weight',
                                'font-style', 'text-decoration', 'color', 'background-color',
                                'letter-spacing', 'word-spacing', 'text-transform', 'white-space'
                            ];
                            
                            styleProps.forEach(prop => {
                                let value = computed.getPropertyValue(prop);
                                
                                // Нормализуем line-height: ограничиваем разумными значениями (1.2-1.6)
                                if (prop === 'line-height' && value) {
                                    const numValue = parseFloat(value);
                                    if (!isNaN(numValue)) {
                                        if (numValue > 1.6) {
                                            value = '1.6';
                                        } else if (numValue < 1.2) {
                                            value = '1.2';
                                        } else {
                                            value = numValue.toString();
                                        }
                                    }
                                }
                                
                                // Сохраняем все значения, кроме дефолтных
                                if (value && 
                                    value !== 'normal' && 
                                    value !== 'none' && 
                                    value !== '0px' && 
                                    value !== '0' && 
                                    value !== 'rgba(0, 0, 0, 0)' && 
                                    value !== 'transparent' &&
                                    value !== 'auto') {
                                    importantStyles.push(`${prop}: ${value}`);
                                }
                            });
                            
                            if (importantStyles.length > 0) {
                                el.setAttribute('style', importantStyles.join('; '));
                            }
                        }
                    });
                    
                    return element;
                };
                
                // Очищаем и сохраняем стили
                cleanAndPreserveStyles(tempDiv);
                
                // Получаем HTML со всеми сохраненными стилями
                const cleanedHTML = tempDiv.innerHTML;
                
                // Вставляем HTML напрямую в DOM редактора, минуя Quill's Delta преобразование
                // Это сохранит все inline стили 1:1
                const range = quill.getSelection(true);
                const currentHTML = quill.root.innerHTML;
                
                if (range) {
                    // Удаляем выделенный текст, если есть
                    if (range.length > 0) {
                        quill.deleteText(range.index, range.length);
                    }
                    
                    // Получаем HTML до и после точки вставки напрямую из DOM
                    // Это сохранит все существующие стили
                    const beforeHTML = quill.root.innerHTML.substring(0, quill.root.innerHTML.length);
                    const textBefore = quill.getText(0, range.index);
                    const textAfter = quill.getText(range.index, quill.getLength() - 1);
                    
                    // Находим позицию вставки в HTML
                    // Простой подход: вставляем новый HTML в конец существующего
                    // Но лучше: находим узел в DOM и вставляем туда
                    const editorHTML = quill.root.innerHTML;
                    
                    // Если редактор пустой или почти пустой, просто заменяем содержимое
                    if (editorHTML.trim() === '' || editorHTML.trim() === '<p><br></p>') {
                        // Убираем все <p><br></p> из начала вставляемого HTML
                        const cleanPastedHTML = cleanedHTML.replace(/^(<p><br><\/p>\s*)+/, '');
                        quill.root.innerHTML = cleanPastedHTML;
                    } else {
                        // Вставляем новый HTML в конец
                        // Убираем последний <p><br></p> если он есть
                        const cleanEditorHTML = editorHTML.replace(/(<p><br><\/p>\s*)+$/, '');
                        // Убираем все <p><br></p> из начала вставляемого HTML
                        const cleanPastedHTML = cleanedHTML.replace(/^(<p><br><\/p>\s*)+/, '');
                        quill.root.innerHTML = cleanEditorHTML + cleanPastedHTML;
                    }
                    
                    // НЕ вызываем quill.update() - это может перезаписать стили
                    // Вместо этого просто обновляем input
                    
                    // Временно отключаем обновление, чтобы избежать циклов с обработчиками событий Quill
                    isUpdating = true;
                    
                    // Обновляем input сразу, но только один раз
            setTimeout(() => {
                        if (isUpdating) {
                updateInput();
                            // Снимаем флаг после обновления
                            setTimeout(() => {
                                isUpdating = false;
                            }, 200);
                        }
                    }, 100);
                    
                    // Устанавливаем курсор в конец
            setTimeout(() => {
                        const newLength = quill.getLength();
                        quill.setSelection(newLength - 1);
                        
                        // Снимаем флаг вставки через 2 секунды после вставки
                        pasteTimeout = setTimeout(() => {
                            isPasting = false;
                        }, 2000);
                    }, 10);
                } else {
                    // Если нет выделения, вставляем в конец
                    const cleanEditorHTML = currentHTML.replace(/(<p><br><\/p>\s*)+$/, '');
                    // Убираем все <p><br></p> из начала вставляемого HTML
                    const cleanPastedHTML = cleanedHTML.replace(/^(<p><br><\/p>\s*)+/, '');
                    
                    // Временно отключаем обновление
                    isUpdating = true;
                    quill.root.innerHTML = cleanEditorHTML + cleanPastedHTML;
                    
                    // Снимаем флаг вставки через 2 секунды после вставки
                    if (pasteTimeout) {
                        clearTimeout(pasteTimeout);
                    }
                    pasteTimeout = setTimeout(() => {
                        isPasting = false;
                    }, 2000);
                    
                    setTimeout(() => {
                        if (isUpdating) {
                            updateInput();
                            setTimeout(() => {
                                isUpdating = false;
                            }, 200);
                        }
                    }, 100);
                }
                
                // Обновляем input один раз после вставки
                setTimeout(() => {
                    if (!isUpdating) {
                        updateInput();
                    }
                }, 100);
                
                return;
            }
            
            // Если нет HTML, используем стандартную обработку Quill
        }, true); // Используем capture phase для перехвата до обработки Quill

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

    // Setup Livewire hook when available
    function setupLivewireQuillHook() {
      
      if (window.Livewire && window.Livewire.hook) {
        
        Livewire.hook("morphed", () => {
          const editors = document.querySelectorAll(".quill-editor");
          editors.forEach((editor) => {
            if (!builded_editors.includes(editor)) {
              makeQuill(editor);
              builded_editors.push(editor);
            }
            // НЕ перезагружаем контент для существующих редакторов - это вызывает конфликты
            // Livewire сам обновит через события input/change
          });
        });
      } else {
        // Wait for Livewire to load
        document.addEventListener('livewire:init', setupLivewireQuillHook, { once: true });
      }
    }
    
    setupLivewireQuillHook();
});
