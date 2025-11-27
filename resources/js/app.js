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
                },
            },
            placeholder: editor.getAttribute("data-placeholder") ?? "",
        });

        // Обработчик вставки из Word с сохранением форматирования
        // Используем более простой подход - обрабатываем только элементы, сохраняя весь текст
        quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
            const Delta = Quill.import('delta');
            const tagName = node.tagName ? node.tagName.toLowerCase() : '';
            
            // Обрабатываем параграфы и div'ы - сохраняем выравнивание и размер
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
            
            // Обрабатываем списки
            if (tagName === 'ul' || tagName === 'ol') {
                const listType = tagName === 'ul' ? 'bullet' : 'ordered';
                if (delta.length() > 0) {
                    delta = delta.compose(new Delta().retain(delta.length(), { list: listType }));
                }
            }
            
            // Обрабатываем элементы списка
            if (tagName === 'li') {
                const parent = node.parentNode;
                if (parent) {
                    const parentTag = parent.tagName ? parent.tagName.toLowerCase() : '';
                    if (parentTag === 'ul' || parentTag === 'ol') {
                        const listType = parentTag === 'ul' ? 'bullet' : 'ordered';
                        if (delta.length() > 0) {
                            delta = delta.compose(new Delta().retain(delta.length(), { list: listType }));
                        }
                    }
                }
            }
            
            // Важно: всегда возвращаем delta, чтобы не потерять текст
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

        setTimeout(() => {
          let content = input?.value || '';
          
          if (!content) {
            return;
          }
          
          // ВАЖНО: Преобразуем правильные HTML теги <ul> и <ol> обратно в формат Quill с data-list
          // Quill использует <ol> для всех списков, но различает их через data-list атрибут на <li>
          
          // Создаем временный контейнер для работы с DOM
          const tempDiv = document.createElement('div');
          tempDiv.innerHTML = content;
          
          // Преобразуем <ul> в <ol> с data-list="bullet" на элементах <li>
          const ulLists = Array.from(tempDiv.querySelectorAll('ul'));
          ulLists.forEach(ul => {
              const ol = document.createElement('ol');
              // Копируем атрибуты (кроме data-list)
              Array.from(ul.attributes).forEach(attr => {
                  if (attr.name !== 'data-list') {
                      ol.setAttribute(attr.name, attr.value);
                  }
              });
              
              // Обрабатываем только прямые дочерние <li> элементы
              Array.from(ul.children).forEach(li => {
                  if (li.tagName === 'LI') {
                      const newLi = li.cloneNode(true);
                      // Удаляем старый data-list, если есть
                      newLi.removeAttribute('data-list');
                      newLi.setAttribute('data-list', 'bullet');
                      
                      // Обрабатываем вложенные списки внутри <li>
                      const nestedUls = newLi.querySelectorAll('ul');
                      nestedUls.forEach(nestedUl => {
                          const nestedOl = document.createElement('ol');
                          Array.from(nestedUl.children).forEach(nestedLi => {
                              if (nestedLi.tagName === 'LI') {
                                  const newNestedLi = nestedLi.cloneNode(true);
                                  newNestedLi.removeAttribute('data-list');
                                  newNestedLi.setAttribute('data-list', 'bullet');
                                  nestedOl.appendChild(newNestedLi);
                              }
                          });
                          nestedUl.parentNode.replaceChild(nestedOl, nestedUl);
                      });
                      
                      const nestedOls = newLi.querySelectorAll('ol');
                      nestedOls.forEach(nestedOl => {
                          Array.from(nestedOl.children).forEach(nestedLi => {
                              if (nestedLi.tagName === 'LI' && !nestedLi.hasAttribute('data-list')) {
                                  nestedLi.setAttribute('data-list', 'ordered');
                              }
                          });
                      });
                      
                      ol.appendChild(newLi);
                  }
              });
              
              ul.parentNode.replaceChild(ol, ul);
          });
          
          // Для <ol> без data-list на элементах <li> добавляем data-list="ordered"
          const olLists = Array.from(tempDiv.querySelectorAll('ol'));
          olLists.forEach(ol => {
              // Проверяем, есть ли data-list на прямых дочерних элементах
              const directListItems = Array.from(ol.children).filter(child => child.tagName === 'LI');
              let hasDataList = false;
              
              directListItems.forEach(li => {
                  if (li.hasAttribute('data-list')) {
                      hasDataList = true;
                  }
              });
              
              // Если нет data-list на элементах, добавляем data-list="ordered"
              if (!hasDataList) {
                  directListItems.forEach(li => {
                      li.setAttribute('data-list', 'ordered');
                  });
              }
              
              // Обрабатываем вложенные списки внутри <ol>
              const nestedUls = ol.querySelectorAll('ul');
              nestedUls.forEach(nestedUl => {
                  const nestedOl = document.createElement('ol');
                  Array.from(nestedUl.children).forEach(nestedLi => {
                      if (nestedLi.tagName === 'LI') {
                          const newNestedLi = nestedLi.cloneNode(true);
                          newNestedLi.removeAttribute('data-list');
                          newNestedLi.setAttribute('data-list', 'bullet');
                          nestedOl.appendChild(newNestedLi);
                      }
                  });
                  nestedUl.parentNode.replaceChild(nestedOl, nestedUl);
              });
          });
          
          // Получаем преобразованный контент
          content = tempDiv.innerHTML;
          
          // Загружаем контент напрямую в Quill через innerHTML
          // Это сохраняет структуру списков
          quill.root.innerHTML = content;
          
          // После загрузки принудительно восстанавливаем списки в формате Quill
          setTimeout(() => {
              const editorElement = quill.root;
              
              // Обрабатываем все <ol> списки - убеждаемся, что у них есть data-list на <li>
              const allOlLists = editorElement.querySelectorAll('ol');
              allOlLists.forEach(ol => {
                  const listItems = Array.from(ol.children).filter(child => child.tagName === 'LI');
                  let hasDataList = false;
                  
                  listItems.forEach(li => {
                      if (li.hasAttribute('data-list')) {
                          hasDataList = true;
                      }
                  });
                  
                  // Если нет data-list, определяем тип по контексту
                  // Если это был <ul> (преобразованный выше), должен быть data-list="bullet"
                  // Иначе data-list="ordered"
                  if (!hasDataList) {
                      // Проверяем, был ли это <ul> - если в БД был <ul>, мы его преобразовали в <ol>
                      // Но мы не можем точно определить, поэтому используем эвристику:
                      // Если список не имеет data-list, значит это был <ol> из БД, добавляем "ordered"
                      listItems.forEach(li => {
                          li.setAttribute('data-list', 'ordered');
                      });
                  }
              });
              
              // Принудительно применяем стили для списков
              const lists = editorElement.querySelectorAll('ul, ol');
              lists.forEach(list => {
                if (list.tagName === 'UL') {
                  list.style.listStyleType = 'disc';
                  list.style.listStyle = 'disc outside';
                } else if (list.tagName === 'OL') {
                  // Определяем тип списка по data-list на первом элементе
                  const firstLi = list.querySelector('li[data-list]');
                  if (firstLi) {
                      const listType = firstLi.getAttribute('data-list');
                      if (listType === 'bullet') {
                          list.style.listStyleType = 'disc';
                          list.style.listStyle = 'disc outside';
                      } else {
                          list.style.listStyleType = 'decimal';
                          list.style.listStyle = 'decimal outside';
                      }
                  } else {
                      // Если нет data-list, по умолчанию ordered
                      list.style.listStyleType = 'decimal';
                      list.style.listStyle = 'decimal outside';
                  }
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
              
              // Обновляем input с преобразованным контентом (в формате Quill)
              if (input) {
                  input.value = editorElement.innerHTML;
              }
          }, 100);
        }, 300);

        // Обработчик изменений текста
        quill.on("text-change", updateInput);
        
        // Обработчик изменений редактора (включая вставку)
        quill.on("editor-change", (eventName, ...args) => {
            if (eventName === 'text-change' || eventName === 'selection-change') {
                // Небольшая задержка для обработки вставки
                setTimeout(updateInput, 10);
            }
        });
        
        // Дополнительный обработчик для вставки через paste - убеждаемся, что весь текст сохраняется
        editor.addEventListener('paste', function(e) {
            // Позволяем Quill обработать вставку стандартным способом
            // Затем принудительно обновляем input
            
            // Для вставки из Word Quill должен обработать HTML автоматически
            // Мы просто убеждаемся, что input обновляется после вставки
            
            setTimeout(() => {
                updateInput();
                // Дополнительное обновление для Livewire с небольшой задержкой
                setTimeout(() => {
                    if (input) {
                        // Триггерим все необходимые события для Livewire
                        input.dispatchEvent(new Event('input', { bubbles: true, cancelable: true }));
                        input.dispatchEvent(new Event('change', { bubbles: true, cancelable: true }));
                        input.dispatchEvent(new Event('blur', { bubbles: true, cancelable: true }));
                    }
                }, 100);
            }, 100);
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
          }
        });
    });
});
