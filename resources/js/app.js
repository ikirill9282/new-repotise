import './bootstrap';
import 'quill/dist/quill.snow.css';

// import { autocomplete } from '@algolia/autocomplete-js'
// import {
//   meilisearchAutocompleteClient,
//   getMeilisearchResults,
// } from '@meilisearch/autocomplete-client'

// import '@algolia/autocomplete-theme-classic'

import AirDatepicker from 'air-datepicker';
import 'air-datepicker/air-datepicker.css';
import localeEn from 'air-datepicker/locale/en';

import Quill from 'quill';

window.Quill = Quill;
window.AirDatepicker = AirDatepicker;
window.createDatePicker = createDatePicker;

function createDatePicker(selector)
{
  return new AirDatepicker(selector, {
      locale: localeEn,
      dateFormat(date) {
        return date.toLocaleString('en-US', {
          year: 'numeric',
          day: '2-digit',
          month: '2-digit',
      });
    }
  });
}

document.addEventListener('DOMContentLoaded', function () {
  const editors = document.querySelectorAll('.quill-editor');
  
  editors.forEach(editor => {
      const quill = new Quill(editor, {
          theme: 'snow',
          modules: {
              toolbar: [
                  ['bold', 'italic', 'underline'],
                  ['link', 'image'],
                  [{ list: 'ordered'}, { list: 'bullet' }],
                  [{ 'align': ['', 'center', 'right', 'justify'] }],
                  ['clean']
              ]
          },
          placeholder: editor.getAttribute('data-placeholder') ?? '',
      });

      quill.on('text-change', () => {
          const content = quill.root.innerHTML;
          editor.value = content;
          
          const counter = editor.closest('.text-editor')?.querySelector('.text-counter');
          if (counter) {
            // @readthedoc
            counter.innerHTML = quill.getLength() - 1;
          }
      });
  });
});