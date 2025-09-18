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
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const editors = document.querySelectorAll(".quill-editor");

    editors.forEach((editor) => {
        const quill = new Quill(editor, {
            theme: "snow",
            modules: {
                toolbar: {
                    container: [
                        ["bold", "italic", "underline"],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        ["link", "image"],
                        [{ list: "ordered" }, { list: "bullet" }],
                        [{ align: ["", "center", "right", "justify"] }],
                        ["clean"],
                    ],
                    handlers: {
                        image: function () {
                            const input = document.createElement("input");
                            input.setAttribute("type", "file");
                            input.setAttribute("accept", "image/*");
                            input.click();

                            input.onchange = () => {
                            const formData = new FormData();
                            formData.append('image', input.files[0]);

                            axios.post('/api/data/upload-image', formData, {
                              headers: {
                                'Content-Type': 'multipart/form-data'
                              }
                            })
                            .then(response => {
                              const data = response.data;
                              if (data.status === 'error') {
                                $.toast({
                                  text: data.message,
                                  icon: 'error',
                                  heading: 'Error',
                                  position: 'top-right',
                                })
                              }
                            })
                            .catch(error => {
                              console.error('Ошибка при загрузке', error);
                            });
                          };
                        },
                    },
                },
            },
            placeholder: editor.getAttribute("data-placeholder") ?? "",
        });


        const id = editor.getAttribute("data-model");
        const wrap = editor.closest(".text-editor");
        const input = wrap?.querySelector(`#${id}`);

        quill.root.innerHTML = input?.value;

        quill.on("text-change", () => {

            if (id && input) {
              const content = quill.root.innerHTML;
              input.value = content;

              const event = new Event("input", {
                  bubbles: true,
                  cancelable: true,
              });

              input.dispatchEvent(event);
            }

            const counter = wrap?.querySelector(".text-counter");
            if (counter) {
                // @readthedoc
                counter.innerHTML = quill.getLength() - 1;
            }
        });
    });
});
